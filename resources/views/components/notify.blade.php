{{--@php--}}
{{--    $message = session('message');--}}
{{--    $show = $message ? true : false;--}}
{{--@endphp--}}
    <div x-data="{show:false, message:null}"
         x-cloak=""
         x-init="
         window.addEventListener('notify', event => {
         message = event.detail.message
         show = true;
         setTimeout(() => {show = false}, 2000)
        })
         "
         x-show="show"
         x-transition.duration.300ms
         class="fixed right-4 top-4 bg-green-400 text-white rounded-full inline-block px-4 py-2">
        <span x-text="message"></span>
    </div>

