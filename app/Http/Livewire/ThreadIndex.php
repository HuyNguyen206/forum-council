<?php

namespace App\Http\Livewire;

use App\Models\Thread;
use App\Services\ThreadsTrending;
use Livewire\Component;
use Livewire\WithPagination;

class ThreadIndex extends Component
{
    use WithPagination, GetThread;

    protected $listeners = ['refresh'];

    public $channel;

    public function render()
    {
        $threads = $this->getThreads($this->channel);
        $trendingThreads = app(ThreadsTrending::class)->get();

        return view('livewire.thread-index', compact('threads', 'trendingThreads'));
    }

    public function refresh()
    {
        $this->render();
    }
}
