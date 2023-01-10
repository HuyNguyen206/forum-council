<article class="p-4">
    <h2 class="font-semibold text-xl mb-4">Create a new channel</h2>
    <form wire:submit.prevent="storeNewChannel">
        @csrf
        <div>
            <label for="">Name</label>
            <input value="{{old('name')}}" class="block @error('name') border-red-500 @enderror" wire:model="name" type="text" name="name">
            <div>
                @error('name')
                <div class="text-red-500 font-semibold">{{$message}}</div>
                @enderror
            </div>

        </div>
        <x-button class="mt-4">Create</x-button>
    </form>
</article>
