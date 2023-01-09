<article class="p-4 flex justify-between">
<p>
    <x-link class="text-xl" href="{{route('users.profile', $user->name_slug)}}">{{$user->name}}</x-link> has replied to
        <x-link :href="route('threads.show', $activity->getThreadSlug())">{{$activity->subject->thread->title}} </x-link>
    <q class="">
        {{$activity->subject->body}}
    </q>

</p>
<div>
    <span class="text-sm text-gray-400">{{ $activity->created_at->diffForHumans()}}</span>
</div>
</article>
