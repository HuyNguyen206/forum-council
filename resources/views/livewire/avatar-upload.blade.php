<div>
    <form wire:submit.prevent="save">
        @if ($photo)
            <img class="rounded-full border w-20 h-20" src="{{ is_object($photo) ? $photo->temporaryUrl() : $photo }}">
        @endif
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
        <input accept="image/*" wire:model="photo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
        @error('photo') <span class="error">{{ $message }}</span> @enderror

        <x-button class="mt-3">Save Photo</x-button>
    </form>
    <x-notify/>
</div>
