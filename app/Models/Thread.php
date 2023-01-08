<?php

namespace App\Models;

use App\Models\Traits\ThreadUpdateHighlight;
use App\Services\ThreadsVisits;
use App\Traits\RecordActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Stevebauman\Purify\Casts\PurifyHtmlOnGet;

class Thread extends Model
{
    use HasFactory, RecordActivity, ThreadUpdateHighlight, Searchable;

    protected $casts = [
        'body' => PurifyHtmlOnGet::class,
    ];

    protected static $unguarded = true;

    protected static function booted()
    {
        static::created(function (Thread $thread) {
            if (auth()->user()) {
                $thread->cacheKey();
            }
        });

        static::creating(function (Thread $thread) {
            $thread->slug = static::generateUniqueSlug(Str::slug($thread->title));
        });

        static::updating(function (Thread $thread){
            $thread->slug = Str::slug($thread->title);
        });
    }

    protected static function generateUniqueSlug($slug)
    {
        $originSlug = $slug;
        if (!static::where('slug', $slug)->exists()) {
            return $slug;
        }

        $count = 2;
        while (static::where('slug', $slug = "$originSlug-$count")->exists()) {
            $count++;
        }

        return $slug;
    }

    public function toggleLockThread()
    {
        $this->update(['is_lock' => !$this->is_lock]);
    }

    public function toggleBestReply($replyId)
    {
        $bestReplyId = $this->best_reply_id === $replyId ? null : $replyId;
        $this->update([
            'best_reply_id' => $bestReplyId
        ]);
    }

    public function showThreadPath()
    {
        return route('threads.show', [$this->id, $this->slug]);
    }

    public function destroyThreadPath()
    {
        return route('threads.destroy', [$this->id, $this->slug]);
    }

//    public function getRouteKeyName()
//    {
//        return 'slug';
//    }

    public function visits()
    {
        return app(ThreadsVisits::class)->visits($this->id);
    }

    public function addReply($reply)
    {
        return $this->replies()->create($reply);
    }

    public function isSubscribeByUser(User $user = null)
    {
        $user ??= auth()->user();

        return $this->subscribeUsers()->where('users.id', $user->id)->exists();
    }

//    public function scopeByName(Builder $builder, $channel, $name)
//    {
//        if ($channel) {
//            $builder = $channel->threads();
//        } else {
//            $builder = Thread::query();
//        }
//
//        return $builder->when($name, fn(Builder $builder) => $builder->whereHas('user',
//                fn($builder) => $builder->whereName($name)));
//    }

    public function subscribeUsers()
    {
        return $this->belongsToMany(User::class, 'thread_subscribed');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
