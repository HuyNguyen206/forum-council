<?php

namespace App\Notifications;

use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MentionedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Reply $reply)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $threadPath = route('threads.show', [$this->reply->thread->slug, $this->reply->thread_id])."#reply-{$this->reply->id}";

        return [
            'reply' => $this->reply,
            'message' => "{$this->reply->user->name} mentioned you in this thread's reply <a href='$threadPath'>link</a>"
        ];
    }
}
