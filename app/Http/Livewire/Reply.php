<?php

namespace App\Http\Livewire;

use App\Exceptions\SpamReplyException;
use App\Rules\CheckSpam;
use App\SpamRules\Spam;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Reply extends Component
{
    use AuthorizesRequests;

    public $reply;
    public $body;
    public $isFavorite;
    public $favoriteUsersCount;
    public $thread;

    public function mount()
    {
        $this->thread = $this->thread ?? $this->reply->thread;

        $this->body = $this->reply->body;
        $this->isFavorite = $this->reply->isFavorite;
        $this->favoriteUsersCount = $this->reply->favoriteUsersCount;
    }

    public function render()
    {
        return view('livewire.reply');
    }

    public function rules()
    {
        return [
            'body' => ['required', new CheckSpam(\App\Models\Reply::class, 'body')]
        ];
    }

    public function toggleBestReply()
    {
        $this->authorize('markBestReply', $this->thread);

        $this->thread->toggleBestReply($this->reply->id);
        $this->emitTo(Replies::class, 'refreshReplies', 'Mark reply as best reply successfully!');
    }

    public function updateReply()
    {
        $this->validate();

        $this->reply->update(['body' => $this->body]);
        $this->dispatchBrowserEvent('notify', ['message' => 'Reply was update successfully!']);
        $this->dispatchBrowserEvent('reply-updated');
    }

    public function deleteReply()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->redirect(route('login'));
        }

        $this->authorize('delete', $this->reply);

        $this->reply->delete();

        $this->emitTo(Replies::class, 'refreshReplies', 'Reply was deleted successfully!');
        $this->emitTo(ThreadInformation::class, 'refreshInfo');
    }

    public function toggleFavoriteReply()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->redirect(route('login'));
        }

        $favoriteReplies =$user->favoriteReplies();
        $favoriteReplies->toggle($this->reply);

        $this->isFavorite = ! $this->isFavorite;
        $this->favoriteUsersCount = $this->reply->loadCount('favoriteUsers as favoriteUsersCount')->favoriteUsersCount;
    }
}
