<article class="p-4">
    <div class="flex justify-between">
        <p>
            <x-link class="text-xl" href="{{route('users.profile', $user->name_slug)}}">{{$user->name}}</x-link> has update thread
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
</article>
