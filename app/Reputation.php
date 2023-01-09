<?php

namespace App;

class Reputation
{
    const THREAD_WAS_CREATED = 10;
    const REPLY_POSTED = 2;
    const BEST_REPLY_AWARDED = 50;
    const REPLY_FAVORITED = 5;

    public static function award($points, $user = null)
    {
        $user ??= auth()->user();

        $user->increment('points', $points);
    }

    public static function reduce($points, $user = null)
    {
        $user ??= auth()->user();
        $user->decrement('points', $points);
    }
}
