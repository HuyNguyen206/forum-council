<?php

namespace App\Http\Livewire;

use App\Models\Channel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class EditChannel extends Component
{
    use AuthorizesRequests;
    public Channel $channel;

    public function render()
    {
        return view('livewire.edit-channel');
    }

    public function rules()
    {
        return [
            'channel.name' => ['required', 'unique:channels,name', 'min:3', 'max:50'],
        ];
    }

    public function updateChannel()
    {
//        $this->authorize('create', Channel::class);
        $this->validate();

        $this->channel->save();

        return $this->redirect(route('channels.index'));
    }
}
