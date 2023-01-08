<?php

namespace App\Models;

use App\Notifications\MentionedNotification;
use App\Traits\RecordActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Stevebauman\Purify\Casts\PurifyHtmlOnGet;

class Reply extends Model
{
    use HasFactory, RecordActivity;

    protected $casts = [
        'body' => PurifyHtmlOnGet::class,
    ];

    protected static $unguarded = true;

    protected static function booted()
    {
        $events = ['creating', 'updating'];
        foreach ($events as $event) {
            static::$event(function (Reply $reply) use($event) {
                Notification::send($reply->getUsersByMentionNames(), new MentionedNotification($reply));
                $reply->body = static::wrapMentionUserNameInAnchor($reply);
                $thread =  $reply->thread;
                $thread->touch();
                $thread->cacheThreadIgnoreUser($reply->user_id);
                static::notifiySubscribeUsers($reply, $event);
            });
        }
    }

    protected static function wrapMentionUserNameInAnchor($reply)
    {
        $resultString = $reply->body;
        $reply->getUsersByMentionNames()->each(function ($user) use(&$resultString) {
            $replaceValue = $user->generateProfileLink();
            $resultString = str_replace($user->name_slug, $replaceValue, $resultString);
        });

        return $resultString;
    }

    public function isBestReply($bestReplyId)
    {
        return $this->id === $bestReplyId;
    }
    public function getUsersByMentionNames()
    {
        return User::query()->whereIn('name_slug', $this->getMentionUserNames())->get();
    }

    public function getMentionUserNames()
    {
        $mentionUserArray = $this->getMentionUserNameArray();

        return $mentionUserArray[1];
    }

    public function replyHash()
    {
        return "reply-{$this->id}";
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function getFavoriteUsersCount()
    {
        return $this->favoriteUsersCount === 0 ? '' : $this->favoriteUsersCount;
    }

    public function favoriteUsers()
    {
        return $this->morphToMany(User::class, 'favorite', 'favorites')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return mixed
     */
    public function getMentionUserNameArray()
    {
        $pattern = "/(?<![\w@])@([\w@\-]+(?:[.!][\w@]+)*)/";
        preg_match_all($pattern, $this->body, $mentionUserNames, PREG_PATTERN_ORDER);

        return $mentionUserNames;
    }
}
