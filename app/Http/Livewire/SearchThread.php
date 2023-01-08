<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SearchThread extends Component
{
    use GetThread;

    public $q = null;

    public function render()
    {
        $threads = $this->getThreads();
        return view('livewire.search-thread', compact('threads'));
    }

    public function search()
    {
        $this->render();
        $this->dispatchBrowserEvent('search');
    }



}
