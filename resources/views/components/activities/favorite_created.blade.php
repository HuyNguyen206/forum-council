@php
$reply = $activity->subject->getReply();
@endphp
<article class="p-4 flex justify-between">
    <p>
        <x-link class="text-xl" href="{{route('users.profile', $user->name_slug)}}">{{$user->name}}</x-link> has favorite the reply of thread <x-link href="{{$reply->thread->showThreadPath().'#'.$reply->replyHash()}}">{{$reply->thread->title}} </x-link>
        <q class="">
            {{$reply->body}}
        </q>

    </p>
    <div>
        <span class="text-sm text-gray-400">{{ $activity->created_at->diffForHumans()}}</span>

    </div>
</article>
