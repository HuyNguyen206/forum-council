<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Rules\CheckSpam;
use Livewire\Component;

class MentionNewReply extends Component
{
    public $body;
    public $thread;

    public function render()
    {
        return view('livewire.mention-new-reply');
    }

    public function getSuggestName()
    {
        $suggestName = User::query()->where('name', 'like', "{$this->body}%")
            ->orWhere('name_slug', 'like', "{$this->body}%")
            ->take(5)
            ->get(['name', 'name_slug', 'id'])
            ->toArray();

        $this->dispatchBrowserEvent('show-suggest-name', ['data' => $suggestName]);
    }

    public function rules()
    {
        return [
            'body' => ['required', new CheckSpam(\App\Models\NewReply::class, 'body')]
        ];
    }

    public function storeReply()
    {
        if (auth()->guest()) {
            return $this->redirect(route('login'));
        }

        $this->validate();

        $this->thread->addReply(['body' => $this->body, 'user_id' => auth()->id()]);

        $this->emitTo(Replies::class, 'refreshReplies', 'The reply was created successfully');
        $this->emitTo(ThreadInformation::class, 'refreshInfo', 'create');
        $this->body = '';
    }
}
