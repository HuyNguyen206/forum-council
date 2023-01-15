<?php

namespace App\Http\Livewire\Action;

use App\Models\Favorite;
use App\Models\Reply;

class RepliesBuilder
{
    public static function build($thread)
    {
        $user = auth()->user();
        $replies = $thread->replies()->with('user')->withCount('favoriteUsers as favoriteUsersCount');
        if ($user) {
            $replies->addSelect([
                'isFavorite' => Favorite::query()->select('id')
                    ->where('favorite_type', Reply::class)
                    ->whereColumn('favorite_id', 'replies.id')
                    ->where('user_id', $user->id)
            ]);
        }

        return $replies->latest('id');
    }
}
