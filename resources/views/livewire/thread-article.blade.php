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
        @if($enableAction)
            <div class="flex space-x-2">
                @can('create', \App\Models\Thread::class)
                    <form wire:submit.prevent="pinThread({{$thread->id}})">
                        @csrf
                        @method('delete')
                        <x-button color="bg-green-800 text-white">Pin Thread</x-button>
                    </form>
                @endcan

                @can('delete', $thread)
                    <form action="{{$thread->destroyThreadPath()}}" method="post">
                        @csrf
                        @method('delete')
                        <x-button color="bg-red-400 text-white">Delete</x-button>
                    </form>
                @endcan
            </div>
        @endif

        <a class="inline-block w-1/12 text-right" href="">{{$thread->repliesCount}} {{\Illuminate\Support\Str::plural('reply', $thread->repliesCount)}}</a>
    </div>
</article>
