<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class UserNotification extends Component
{
    use WithPagination;

    public $user;
    public $unreadNotificationCount;

    public function mount()
    {
        $this->user = auth()->user();
    }
    public function render()
    {
        if (!$this->user) return;

        $unreadNotificationsQuery = $this->user->unreadNotifications();
        $this->unreadNotificationCount = $unreadNotificationsQuery->count();

        $unreadNotifications = $unreadNotificationsQuery->orderBy('notifications.id', 'desc')->simplePaginate(5, pageName: 'page-notification')->withQueryString() ?? [];

        return view('livewire.user-notification', compact('unreadNotifications'));
    }

    public function markNotificationAsRead($notificationId)
    {
        optional($this->user->unreadNotifications()->where('id', $notificationId)->update(['read_at' => now()]));
        $this->render();
    }
}
