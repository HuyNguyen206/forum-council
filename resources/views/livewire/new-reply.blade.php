<div class="">
    @if(!$thread->is_lock)
        <form wire:submit.prevent="storeReply">
            @csrf
            <input type="hidden" wire:model="captchaToken">
{{--            <textarea id="" cols="30" rows="10" class="w-full @error('body') border-red-500 @enderror"--}}
{{--                      wire:model="body"></textarea>--}}
            <livewire:trix body="{!! $body !!}" uniqueId="reply-trix-editor"/>
            <div>
                @error('body')
                <div class="text-red-500 mt-2">{{$message}}</div>
                @enderror
            </div>


            <div wire:ignore>
                <div id="replyCaptcha"></div>
            </div>
            <div>
                <div>
                    @error('captchaToken')
                    <span class="text-red-500 mt-2 ml-2">  {{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                        class="px-4 py-2 rounded-full inline-block bg-blue-600 text-white">
                    Submit
                </button>
            </div>
        </form>
    @else
        <div class="text-center flex justify-center">
                      <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round"
        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
</svg>
</span>
            <p>This thread is already locked by admin.</p>
        </div>
    @endif
    @push('scripts')
        {{--        <script src="https://www.google.com/recaptcha/api.js?render={{config('services.google.captcha_site_key')}}"></script>--}}
        {{--        <script>--}}
        {{--            function handle(e) {--}}
        {{--                console.log('print..');--}}
        {{--                alert('esdsd')--}}
        {{--                e.preventDefault()--}}
        {{--                grecaptcha.ready(function () {--}}
        {{--                    grecaptcha.execute('{{config('services.google.captcha_site_key')}}', {action: 'submit'})--}}
        {{--                        .then(function (token) {--}}
        {{--                        @this.set('captcha', token);--}}
        {{--                        });--}}
        {{--                })--}}
        {{--            }--}}
        {{--            --}}
        {{--        </script>--}}
        <script
            src="https://www.google.com/recaptcha/api.js?onload=handleRecaptchaLoad&render=explicit"
            async
            defer
        ></script>

        <script>
            function handleRecaptchaLoad() {
                grecaptcha.render(
                    'replyCaptcha', {
                        'sitekey': '{{ config('services.google.captcha_site_key') }}',
                        'callback': `replyCaptchaSubmit`
                    }
                )
            }

            function replyCaptchaSubmit(captchaToken) {
            @this.set('captchaToken', captchaToken)
            }

            window.addEventListener('reset-google-recaptcha', () => {
                grecaptcha.reset()
            })

            window.addEventListener('reset-body', event => {
                var element = document.querySelector("trix-editor[input=reply-trix-editor]")
                console.log(element);
                element.editor.loadHTML("")
            })

        </script>

    @endpush
</div>
