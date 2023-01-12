<article class="p-4">
    <div class="flex justify-between">
        <p>
            <x-link class="text-xl" href="{{route('users.profile', $user->name_slug)}}">{{$user->name}}</x-link> has published to thread
            <x-link :href="$activity->subject->showThreadPath()">{{$activity->subject->title}}</x-link>
        </p>
        <div>
            <span class="text-sm text-gray-400">{{ $activity->created_at->diffForHumans()}}</span>
        </div>
    </div>
    <hr>
    <p>
        {{$activity->subject->body}}
    </p>
    <div class="pl-4 mt-2">
        <span class="badge-xp inline-block p-3 bg-green-700 text-white">+ {{\App\Reputation::THREAD_WAS_CREATED}} XP</span>
    </div>
</article>
