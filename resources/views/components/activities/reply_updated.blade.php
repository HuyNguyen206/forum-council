<article class="p-4 flex justify-between">
    <p>
        <x-link class="text-xl" href="{{route('users.profile', $user->name_slug)}}">{{$user->name}}</x-link>
        has updated reply in
        <x-link
            href="{{$activity->subject->thread->showThreadPath($activity->subject->id)}}">{{$activity->subject->thread->title}} </x-link>
        <q class="">
            {{$activity->subject->body}}
        </q>

    </p>
    <div>
        <span class="text-sm text-gray-400">{{ $activity->created_at->diffForHumans()}}</span>

    </div>
</article>
<div class="pl-4 mt-2 flex space-x-2">
    @if($activity->subject->isBestReply($activity->subject->thread->best_reply_id))
        <span class="badge-xp inline-block p-3 bg-green-700 text-white">Best reply</span>
    @endif
</div>
