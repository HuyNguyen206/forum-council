<?php

namespace App\Http\Livewire;

use App\Models\Thread;
use Livewire\Component;

class PinThread extends Component
{
    protected $listeners = ['pinThread'];
    public $pinThread;

    public function mount()
    {
        $this->pinThread = Thread::pinThread()->first();
    }

    public function render()
    {
        return view('livewire.pin-thread');
    }

    public function pinThread($threadId)
    {
        if ($this->pinThread) {
            if ($threadId !== $this->pinThread->id) {
                $this->pinThread->update(['is_pin' => 0]);

                $this->pinThread = tap(Thread::find($threadId))->update(['is_pin' => 1]);
            }
        } else {
            $this->pinThread = tap(Thread::find($threadId))->update(['is_pin' => 1]);
        }

        $this->render();
    }
}
