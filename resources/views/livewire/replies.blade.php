<div>
    <div class="replies mt-3 p-3">
        <h2 class="text-xl font-semibold">Replies</h2>
        @foreach($replies as $reply)
            <livewire:reply :reply="$reply" key="{{ now() }}" :wire:key="$reply->id" :thread="$thread"/>
            <hr>
        @endforeach
    </div>
    {{ $replies->links() }}
</div>
