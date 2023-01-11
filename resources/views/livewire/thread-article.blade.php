<article class="p-4 flex justify-between">
    <div class="w-11/12">
        <div class="flex space-x-3">
            <a href="{{$thread->showThreadPath()}}" class="hover:underline @if($thread->hasNewUpdate())font-bold @endif text-xl">{{$thread->title}}</a>
            <a class='text-blue-600 underline' href="{{route('users.profile', $thread->user->name_slug)}}">by {{$thread->user->name}}</a>
            <livewire:avatar-display :profileUser="$thread->user"/>
            {{--                                <x-avatar-display :profile-user="$thread->user"/>--}}
        </div>

        <p>
            {{$thread->body}}
        </p>
        <span class="font-bold">
            views:{{ $thread->visits() }}
        </span>
    </div>
    <div class="flex flex-col">
        @if($thread->is_pin)
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
            @can('create', \App\Models\Thread::class)
                <form wire:submit.prevent="unpinThread">
                    <x-button color="bg-green-800 text-white">Unpin Thread</x-button>
                </form>
            @endcan
        @endif

        @if($enableAction)
            <div class="flex space-x-2">
                @can('create', \App\Models\Thread::class)
                    <form wire:submit.prevent="pinThread">
                        <x-button color="bg-green-800 text-white">Pin Thread</x-button>
                    </form>
                @endcan

                @can('delete', $thread)
                    <form action="{{$thread->destroyThreadPath()}}" method="post">
                        <x-button color="bg-red-400 text-white">Delete</x-button>
                    </form>
                @endcan
            </div>
        @endif

        <a class="inline-block w-1/12 text-right" href="">{{$thread->repliesCount}} {{\Illuminate\Support\Str::plural('reply', $thread->repliesCount)}}</a>
    </div>
</article>
