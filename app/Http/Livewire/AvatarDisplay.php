<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AvatarDisplay extends Component
{
    protected $listeners = ['updateAvatar'];

    public $profileUser;

    public function render()
    {
        return view('livewire.avatar-display');
    }

    public function updateAvatar()
    {
        $this->render();
    }
}
