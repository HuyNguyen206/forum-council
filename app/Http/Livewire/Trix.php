<?php

namespace App\Http\Livewire;

use Livewire\Component;

/**
 * Please implement trixUpdatedBody listener(Php side)
 * and reset-body listener(in JS side) for livewire component which use this trix component.
 * Can refer to ThreadShow component for example
 */
class Trix extends Component
{
    public $body;
    public $uniqueId;

    public function mount($body = '', $uniqueId = null)
    {
        $this->body = $body;
        $this->uniqueId = $uniqueId ?? uniqid('', true);
    }

    public function render()
    {
        $body = $this->body;
        return view('livewire.trix', compact('body'));
    }

    public function updatedBody($body)
    {
        $this->emit('trixUpdatedBody', $body);
    }
}
