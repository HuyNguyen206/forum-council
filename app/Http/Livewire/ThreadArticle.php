<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ThreadArticle extends Component
{
    public $thread;

    public function render()
    {
        return view('livewire.thread-article');
    }
}
