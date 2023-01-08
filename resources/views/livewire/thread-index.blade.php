<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-12 gap-3">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg  col-span-8 ">
        @foreach($threads as $thread)
            <livewire:thread-article :thread="$thread" wire:key="thread-{{$thread->id}}"/>
            <hr>
        @endforeach
        {!!  $threads->links() !!}
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-4">
        <div>
            <livewire:search-thread/>
        </div>
        <div>
            <h3 class="font-semibold text-xl">Trending threads</h3>
            @foreach($trendingThreads as $trendingThread)
                <livewire:thread-article :thread="$trendingThread" wire:key="trending-thread-{{$trendingThread->id}}"/>
            @endforeach
        </div>
    </div>
</div>
