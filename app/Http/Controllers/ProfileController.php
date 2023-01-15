<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(User $user)
    {
        $activities = $user->activities()->with([
            'subject' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Reply::class => ['thread']
                ]);
            }])
            ->oldest()
            ->paginate(5);

        $activitiesGroupByDate = $activities->groupBy(fn($activity) => $activity->created_at->format('Y-m-d'));
//        dd($activitiesGroupByDate);
//        $threads = Thread::query()
//            ->whereBelongsTo($user)
//            ->withCount('replies as repliesCount')
//            ->latest()
//            ->paginate(5);

        return view('profiles.show', compact('user', 'activitiesGroupByDate', 'activities'));
    }
}
