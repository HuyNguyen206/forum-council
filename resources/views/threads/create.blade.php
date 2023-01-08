<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <article class="p-4">
                    <h2 class="font-semibold text-xl mb-4">Create a new thread</h2>
                    <form action="{{route('threads.store')}}" method="post">
                        @csrf
                        <div class="mb-2">
                            <select name="channel_id" id="" class="@error('channel_id') border-red-500 @enderror">
                                <option value="">Select channel</option>
                                @foreach($channels as $channel)
                                    <option @selected(old('channel_id') == $channel->id) value="{{$channel->id}}">{{$channel->name}}</option>
                                @endforeach
                            </select>
                            @error('channel_id')
                            <div class="text-red-500 font-semibold">{{$message}}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="">Title</label>
                            <input value="{{old('title')}}" class="block @error('title') border-red-500 @enderror" type="text" name="title">
                            @error('title')
                            <div class="text-red-500 font-semibold">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="mt-2">
                            <label for="">Body</label>
                            <textarea class="w-full  @error('body') border-red-500 @enderror" name="body" id="" cols="30" rows="10">{{old('body')}}</textarea>
                            @error('body')
                            <div class="text-red-500 font-semibold">{{$message}}</div>
                            @enderror
                        </div>


                        <x-button>Publish</x-button>
                    </form>
                </article>
            </div>
        </div>
    </div>
    <x-notify/>
</x-app-layout>
