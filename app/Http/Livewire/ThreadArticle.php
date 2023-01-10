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

    public function pinThread($threadId)
    {
        $this->emitTo(PinThread::class, 'pinThread', $threadId);
    }
}
