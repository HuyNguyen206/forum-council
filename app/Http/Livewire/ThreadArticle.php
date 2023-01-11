<?php

namespace App\Http\Livewire;

use App\Models\Thread;
use Livewire\Component;

class ThreadArticle extends Component
{
    public $thread;
    public $enableAction = true;

    public function render()
    {
        return view('livewire.thread-article');
    }

    public function pinThread()
    {
        $this->thread->update(['is_pin' => 1]);

        $this->emitTo(ThreadIndex::class, 'refresh');
    }

    public function unpinThread()
    {
        $this->thread->update(['is_pin' => 0]);

        $this->emitTo(ThreadIndex::class, 'refresh');
    }
}
