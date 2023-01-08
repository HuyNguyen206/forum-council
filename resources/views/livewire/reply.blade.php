<div>
    <div class="mt-2" id="{{$reply->replyHash()}}" x-data="{isEdit:false}">
        <div class="flex justify-between my-2">
            <h3>
                <x-link class="text-xl"
                        href="{{route('users.profile', $reply->user->name_slug)}}">{{$reply->user->name}}</x-link>
                has replied
                at {{$reply->created_at->diffForHumans()}}</h3>
            @auth
                <div class="flex justify-end">
                    <x-button @click="isEdit=true" x-show="!isEdit">Edit</x-button>
                    @can('delete', $reply)
                        <form wire:submit.prevent="deleteReply" method="post">
                            @csrf
                            <x-button color="bg-red-600 text-white border">Delete</x-button>
                        </form>
                    @endcan
                    <form wire:submit.prevent="toggleFavoriteReply">
                        @csrf
                        @if($isFavorite)
                            <x-button
                                color="bg-green-600 text-white border flex">
                                <span>
                                    {{$reply->getFavoriteUsersCount()}}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6 bg-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                </svg>
                            </x-button>
                        @else
                            <x-button
                                color="border-gray-700 bg-white text-gray-900 border">{{$reply->getFavoriteUsersCount()}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                </svg>
                            </x-button>
                        @endif
                    </form>
                </div>
                @php
                    $isBestReply = $reply->isBestReply($thread->best_reply_id);
                @endphp
                @can('markBestReply', $thread)
                    <x-button wire:click="toggleBestReply">{{$isBestReply ? 'UnMark' : 'Mark'}} as best Reply</x-button>
                @endcan
            @endauth
            @if($reply->isBestReply($thread->best_reply_id))
                <span class="bg-green-400 text-white px-4 py-2 rounded-full flex items-center">BestReply</span>
            @endif
        </div>

        <form x-cloak wire:submit.prevent="updateReply" x-show="isEdit" @reply-updated.window="isEdit = false">
            @csrf
            @method('put')
            <textarea autocomplete="" wire:model="body" id="" cols="30" rows="10" class="w-full"></textarea>
            <div>
                <x-button>Update</x-button>
                <x-button @click.prevent="isEdit = false">Cancel</x-button>
            </div>
        </form>
        <p class="" x-show="!isEdit">{!! $reply->body !!}</p>
    </div>
    <x-notify/>
</div>
