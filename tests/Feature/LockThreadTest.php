<?php

namespace Tests\Feature;

use App\Http\Livewire\NewReply;
use App\Http\Livewire\ThreadInformation;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;
use Tests\Traits\VerifyEmail;

class LockThreadTest extends TestCase
{
    use RefreshDatabase, RefreshRedis, VerifyEmail;

    public function test_an_admin_can_lock_any_thread()
    {
        $admin = $this->signIn(create(User::class, states: ['admin']));

        $this->verifyEmail($admin);
        $thread = create(Thread::class);

       $newReplyLivewire = Livewire::actingAs(create(User::class))->test(NewReply::class, ['thread' => $thread])
            ->set('body', 'test')->call('storeReply')
            ->assertStatus(200);

        Livewire::actingAs($admin)->test(ThreadInformation::class, ['thread' => $thread])
            ->call('toggleLockThread');

        Livewire::actingAs(create(User::class))->test(NewReply::class, ['thread' => $thread->fresh()])
            ->set('body', 'test')
            ->call('storeReply')
            ->assertStatus(403);
    }

    public function test_non_admin_user_can_not_lock_any_thread()
    {
        $user = $this->signIn(create(User::class));
        $this->verifyEmail($user);
        $thread = create(Thread::class);

       $newReplyLivewire = Livewire::actingAs(create(User::class))->test(NewReply::class, ['thread' => $thread])
            ->set('body', 'test')->call('storeReply')
            ->assertStatus(200);

        Livewire::actingAs($user)->test(ThreadInformation::class, ['thread' => $thread])
            ->call('toggleLockThread')->assertStatus(403);
    }
}
