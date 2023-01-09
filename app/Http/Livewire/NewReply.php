<?php

namespace App\Http\Livewire;

use App\Exceptions\SpamReplyException;
use App\Rules\CaptchaVerify;
use App\Rules\CheckSpam;
use App\SpamRules\Spam;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class NewReply extends Component
{
    use AuthorizesRequests;

    public $body;
    public $thread;
    protected $listeners = ['refresh', 'trixUpdatedBody'];
    public $captchaToken;

    public function render()
    {
        return view('livewire.new-reply');
    }

    public function rules()
    {
        return [
            'body' => ['required', new CheckSpam(\App\Models\NewReply::class, 'body')],
            'captchaToken' => [new CaptchaVerify] ,
        ];
    }

    public function resetRecaptchaComponent()
    {
        $this->dispatchBrowserEvent('reset-google-recaptcha');
    }

    public function storeReply()
    {
        if (auth()->guest()) {
            return $this->redirect(route('login'));
        }

        abort_if($this->thread->is_lock, 403);
        $this->resetRecaptchaComponent();
        $this->validate();

        $this->reset('captchaToken');
        $this->thread->addReply(['body' => $this->body, 'user_id' => auth()->id()]);

        $this->emitTo(Replies::class, 'refreshReplies', 'The reply was created successfully');
        $this->emitTo(ThreadInformation::class, 'refreshInfo', 'create');
        $this->dispatchBrowserEvent('reset-body');
        $this->body = '';

    }

    public function refresh()
    {
        $this->thread->refresh();
    }

    public function trixUpdatedBody($body)
    {
        $this->body = $body;
    }
}
