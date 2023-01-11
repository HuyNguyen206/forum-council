<?php

namespace App\Http\Livewire;

use App\Models\Channel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NewChannel extends Component
{
    use AuthorizesRequests;

    public $name;

    public function rules()
    {
        return [
            'name' => ['required', 'min:3', 'max:50'],
        ];
    }

    public function render()
    {
        return view('livewire.new-channel');
    }

    public function storeNewChannel()
    {
        $this->authorize('create', Channel::class);
        $this->validate();

        Channel::create([
           'name' => $this->name
        ]);

        return $this->redirect(route('channels.index'));
    }
}
