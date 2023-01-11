<article class="p-4">
    <h2 class="font-semibold text-xl mb-4">Update channel</h2>
    <form wire:submit.prevent="updateChannel">
        @csrf
        <div>
            <label for="">Name</label>
            <input value="{{old('channel.name')}}" class="block @error('name') border-red-500 @enderror" wire:model="channel.name" type="text">
            <div>
                @error('channel.name')
                <div class="text-red-500 font-semibold">{{$message}}</div>
                @enderror
            </div>

        </div>
        <x-button class="mt-4">Update</x-button>
    </form>
</article>
