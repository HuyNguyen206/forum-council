<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ThreadShow extends Component
{
    use AuthorizesRequests;

    public $thread;
    public $channels;
    public $title;
    public $body;
    public $channel_id;
    public $original;

    protected $listeners = ['trixUpdatedBody'];

    public function mount($thread)
    {
        $this->thread = $thread;
        $this->original = $this->thread->body;
        $this->resetThread();
    }


    protected function rules()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'channel_id' => 'required|exists:channels,id'
        ];
    }

    public function render()
    {
        return view('livewire.thread-show');
    }

    public function trixUpdatedBody($body)
    {
        $this->body = $body;
    }


    public function updateThread()
    {
        $this->authorize('delete', $this->thread);

        $validated = $this->validate();

        $this->thread->update($validated);
        $this->original = $this->body;

        $this->dispatchBrowserEvent('thread-updated');
        $this->dispatchBrowserEvent('notify', ['message' => 'Your thread was update successfully!']);
        $this->dispatchBrowserEvent('updated-url', ['url' => route('threads.show', $this->thread->slug)]);
    }

    public function resetData()
    {
        $this->dispatchBrowserEvent('reset-body', ['body' => $this->original]);
        $this->resetThread();
    }

    public function resetThread()
    {
        $this->title = $this->thread->title;
        $this->body = $this->thread->body;
        $this->channel_id = $this->thread->channel_id;
    }
}
