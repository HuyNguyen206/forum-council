<?php

namespace Tests\Feature;

use App\Http\Livewire\NewReply;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\MentionedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->session(['skipCaptchaValidation' => true]);
    }

    public function test_user_receive_notification_when_he_was_mentioned_by_another_user()
    {

        $huy = create(User::class);
        $nhung = create(User::class, ['name' => 'nhung', 'name_slug' => 'nhung', 'email' => 'nhung@gmail.com']);
        $nam = create(User::class, ['name' => 'nam nguyen', 'name_slug' => 'nam-nguyen', 'email' => 'namNguyen@gmail.com']);

        $thread = create(Thread::class);
        Notification::fake();

        Livewire::actingAs($huy)->test(NewReply::class, ['thread' => $thread])
            ->set('body', '@nhung hi @nam-nguyen')
            ->call('storeReply');

        Notification::assertSentTo($nhung, MentionedNotification::class);
        Notification::assertSentTo($nam, MentionedNotification::class);
    }
}
