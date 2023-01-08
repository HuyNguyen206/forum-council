<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ThreadInformation extends Component
{
    use AuthorizesRequests;

    public $thread;
    public $threadRepliesCount;
    public $action;
    public $color;
    public $isSubscribe;

    protected $listeners = ['refreshInfo'];

    public function mount()
    {
        if (auth()->check()) {
            $this->isSubscribe = $this->thread->isSubscribeByUser();
            $this->updateAttribute();
        }

        $this->threadRepliesCount = $this->thread->repliesCount;
    }

    public function render()
    {
        return view('livewire.thread-information');
    }

    public function toggleLockThread()
    {
        $this->authorize('canLockThread', User::class);
        $this->thread->toggleLockThread();
        $this->emitTo(NewReply::class, 'refresh');
    }

    public function markNotificationAsRead($notificationId)
    {
        auth()->user()->unreadNotifications()->findOrFail($notificationId)->markAsRead();
    }

    public function clearNotification()
    {
        if (! $user = auth()->user()) return;

        $user->unreadNotifications->markAsRead();
    }

    public function toggleSubscribe()
    {
        if (auth()->guest()) return $this->redirect(route('login'));

        $this->thread->subscribeUsers()->toggle(auth()->user());
        $this->isSubscribe = ! $this->isSubscribe;
        $this->dispatchBrowserEvent('notify', ['message' => "You {$this->action} successfully"]);

        $this->updateAttribute();
    }

    public function refreshInfo($type = 'delete')
    {
        if ($type === 'delete') {
            $this->threadRepliesCount--;
        }

        if ($type === 'create') {
            $this->threadRepliesCount++;
        }
    }

    /**
     * @return void
     */
    protected function updateAttribute(): void
    {
        $this->action = $this->isSubscribe ? 'Unsubscribe' : 'Subscribe';
        $this->color = $this->isSubscribe ? 'bg-green-500 text-white' : 'bg-white text-black border-2';
    }

}
