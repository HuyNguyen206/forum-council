<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
        <div x-data="{edit:false}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-2/3 pr-2" x-cloak
             @thread-updated.window="edit = false"
        >
            <article class="p-4" x-show="!edit">
                <div class="flex justify-between">
                    <h2 class="font-semibold text-xl">
                        <a href="" class="text-blue-500">{{$thread->user->name}}</a>
                        posted {{$thread->title}}
                    </h2>
                    <span>{{$thread->user->getPointFormat()}}</span>
                </div>
                <hr>
                <p>
                    {!! $thread->body !!}
                </p>
            </article>
            <article class="p-4" x-show="edit">
                <h2 class="font-semibold text-xl mb-4">Edit thread</h2>
                <form wire:submit.prevent="updateThread">
                    <div class="mb-2">
                        <select wire:model="channel_id" id="" class="@error('channel_id') border-red-500 @enderror">
                            <option wire:key="0" value="">Select channel</option>
                            @foreach($channels as $channel)
                                <option wire:key="channel-{{$channel->id}}"
                                        value="{{$channel->id}}" @selected(old('channel_id', $thread->channel_id) == $channel->id)>{{$channel->name}}</option>
                            @endforeach
                        </select>
                        @error('channel_id')
                        <div class="text-red-500 font-semibold">{{$message}}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="">Title</label>
                        <input class="block @error('title') border-red-500 @enderror" type="text" wire:model="title">
                        @error('title')
                        <div class="text-red-500 font-semibold">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <label for="">Body</label>
{{--                        <textarea id="body"  class="w-full  @error('body') border-red-500 @enderror" wire:model="body" id=""--}}
{{--                                  cols="30" rows="10"></textarea>--}}
                        <livewire:trix body="{!! $body !!}" uniqueId="trix-thread-edit"/>
                        @error('body')
                        <div class="text-red-500 font-semibold">{{$message}}</div>
                        @enderror
                    </div>
                    <div>
                        <x-button>Update</x-button>
                        <x-button type="button" wire:click="resetData" @click="edit=false">Cancel</x-button>
                    </div>
                </form>
            </article>
            @can('delete', $thread)
            <x-button x-show="!edit" @click="edit = true">Edit</x-button>
            @endcan
            <livewire:replies :thread="$thread"/>
            @auth()
                {{--                    <livewire:mention-new-reply :thread="$thread"/>--}}
                {{--                    <livewire:ckeditor :thread="$thread"/>--}}
                <livewire:new-reply :thread="$thread"/>
            @endauth
            @guest()
                <div class="text-center">
                    <a href="{{route('login')}}">Please sign in to reply</a>
                </div>

            @endguest
        </div>
        <livewire:thread-information :thread="$thread"/>
    </div>
    @push('scripts')
        <script>
            window.addEventListener('updated-url', event => {
                history.pushState(null, null, event.detail.url);
            })
            window.addEventListener('reset-body', event => {
                var element = document.querySelector("trix-editor[input=trix-thread-edit]")
                element.editor.loadHTML(event.detail.body)
            })

        </script>
    @endpush
</div>
