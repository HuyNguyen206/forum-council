<?php

namespace App\Traits;

use App\Models\Activities;
use App\Models\Favorite;
use App\Models\Reply;
use App\Models\Thread;
use App\Notifications\ThreadUpdateNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

trait RecordActivity
{
    protected static array $recordEvents = ['created', 'deleting', 'updated'];

    protected static function bootRecordActivity()
    {
        foreach (static::$recordEvents as $event) {
            static::$event(function (Model $model) use ($event) {
                if ($isAuth = auth()->check()) {
                    if ($event === 'deleting') {
                        static::handleDeleting($model);
                    } else {
                        $model->activities()->create(['type' => strtolower(class_basename($model)) . "_$event", 'user_id' => auth()->id()]);
                    }
                }
            });
        }
    }

    public function activities()
    {
        return $this->morphMany(Activities::class, 'subject');
    }

    /**
     * @param Model $model
     * @return void
     */
    static function handleDeleting(Model $model): void
    {
        $model->activities()->delete();
        if ($model instanceof Thread) {
            Activities::query()->whereHasMorph('subject', Reply::class, function (Builder $builder) use ($model) {
                $builder->whereBelongsTo($model);
            })->delete();
        }

        if ($model instanceof Reply) {
            $favoriteQuery = Favorite::query()->where('favorite_type', Reply::class)->where('favorite_id', $model->id);
            $favoriteIds = $favoriteQuery->get(['id'])->toArray();
            $favoriteQuery->delete();

            Activities::query()->where('subject_type', Favorite::class)->whereIn('subject_id', $favoriteIds)->delete();
//            $favorites->each(function ($favorite) {
//                $favorite->activities()->delete();
//            });
        }
    }

    /**
     * @param Reply $this
     * @param string $subject
     * @param mixed $event
     * @return void
     */
    static function notifiySubscribeUsers(Reply $reply, mixed $event): void
    {
        $subscribeUsers = $reply->thread->subscribeUsers->where('id', '<>', $reply->user_id);
        $ownerThread = $reply->thread->user;
        $subscribeUsers = $subscribeUsers->push($ownerThread)->where('id', '<>', auth()->id());

        $routeThread = route('threads.show',[$reply->thread->slug,  $reply->thread->id]);
        $title = $reply->thread->title;
        Notification::send($subscribeUsers, new ThreadUpdateNotification("A reply was $event in thread <a style='font-weight: bold; color:blue' href='{$routeThread}'>$title</a>", $event));
    }

}
