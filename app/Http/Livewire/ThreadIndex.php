<?php

namespace App\Http\Livewire;

use App\Services\ThreadsTrending;
use Livewire\Component;
use Livewire\WithPagination;

class ThreadIndex extends Component
{
    use WithPagination, GetThread;

    public $channel;

    public function render()
    {
        $threads = $this->getThreads($this->channel);
        $trendingThreads = app(ThreadsTrending::class)->get();

        return view('livewire.thread-index', compact('threads', 'trendingThreads'));
    }
}
