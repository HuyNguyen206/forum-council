<div x-data="{suggestNames:null, isShow:false}" x-init="
            window.addEventListener('show-suggest-name', event => {
             isShow = true
             suggestNames = event.detail.data;
                let postion = $('#body').caret('position');
                let left = postion.left;
                let top = postion.top;

                $('#list').css('left', left);
                $('#list').css('top', top);

                console.log(postion)


            console.log(suggestNames);
        })
        ">
    <div style="position: relative">
        <form wire:submit.prevent="storeReply">
            <textarea id="body" cols="30" rows="10" class="w-full @error('body') border-red-500 @enderror" wire:model.debounce.300ms="body" wire:keydown="getSuggestName"></textarea>
            @error('body')
            <div class="text-red-500 mt-2">{{$message}}</div>
            @enderror
            <button type="submit" class="px-4 py-2 rounded-full inline-block bg-blue-600 text-white">
                Submit
            </button>
        </form>
        <ul id="list" x-show="isShow" style="position: absolute">
            <template x-for="name in suggestNames" :key="name.id">
                <li x-text="name.name"></li>
            </template>
        </ul>
    </div>

</div>
@push('scripts')
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script defer src="{{asset('js/caret.js')}}"></script>
@endpush
