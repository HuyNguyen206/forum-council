<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;
use Tests\Traits\VerifyEmail;

class BestReplyTest extends TestCase
{
    use RefreshDatabase, RefreshRedis, VerifyEmail;

    public function test_a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        $this->withoutExceptionHandling();
        $this->verifyEmail($user = $this->signIn());

        $thread = create(Thread::class, ['user_id' => $user->id]);

        $reply1 = create(Reply::class, ['thread_id' => $thread->id]);
        $reply2 = create(Reply::class, ['thread_id' => $thread->id]);
        $reply3 = create(Reply::class, ['thread_id' => $thread->id]);
        $reply4 = create(Reply::class, ['thread_id' => $thread->id]);

        Livewire::test(\App\Http\Livewire\Reply::class, ['thread' => $thread, 'reply' => $reply2])
            ->call('toggleBestReply');
        self::assertEquals($thread->fresh()->best_reply_id, $reply2->id);

    }

    public function test_only_thread_owner_can_mark_best_reply()
    {
        $this->verifyEmail($user = $this->signIn());

        $thread = create(Thread::class, ['user_id' => $user->id]);

        $reply1 = create(Reply::class, ['thread_id' => $thread->id]);
        $reply2 = create(Reply::class, ['thread_id' => $thread->id]);

        Livewire::actingAs(create(User::class))->test(\App\Http\Livewire\Reply::class, ['thread' => $thread, 'reply' => $reply2])
            ->call('toggleBestReply')
            ->assertStatus(403);
    }

    public function test_thread_owner_can_unmark_best_reply()
    {
        $this->verifyEmail($user = $this->signIn());

        $thread = create(Thread::class, ['user_id' => $user->id]);

        $reply1 = create(Reply::class, ['thread_id' => $thread->id]);
        $reply2 = create(Reply::class, ['thread_id' => $thread->id]);

        $livewire = Livewire::test(\App\Http\Livewire\Reply::class, ['thread' => $thread, 'reply' => $reply2])
            ->call('toggleBestReply');
        self::assertEquals($thread->fresh()->best_reply_id, $reply2->id);
        self::assertNotNull($thread->fresh()->best_reply_id);
        $livewire ->call('toggleBestReply');
        self::assertNull($thread->fresh()->best_reply_id);
        self::assertNotEquals($thread->fresh()->best_reply_id, $reply2->id);
    }

}
