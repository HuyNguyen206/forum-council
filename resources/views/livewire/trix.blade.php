<div>
    <input id="{{$uniqueId}}"  value="{!! $body !!}" type="hidden" name="content">
    <div wire:ignore>
        <trix-editor input="{{$uniqueId}}"></trix-editor>
    </div>
    @push('styles')
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    @endpush
    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
        <script>
            var trixEditor = document.getElementById("{{ $uniqueId }}")

            addEventListener("trix-change", function(event) {
            @this.set('body', trixEditor.getAttribute('value'))
            })
        </script>
    @endpush
</div>
