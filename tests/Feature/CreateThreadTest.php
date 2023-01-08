<?php

namespace Tests\Feature;

use App\Http\Livewire\ThreadShow;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;
use Tests\Traits\VerifyEmail;

class CreateThreadTest extends TestCase
{
    use RefreshDatabase, RefreshRedis, VerifyEmail;

    public function test_authenticate_user_can_creat_a_new_forum_threads()
    {
        $this->withoutExceptionHandling();
        $this->verifyEmail($this->signIn(create(User::class, ['email_verified_at' => null])));

        $channel = Channel::factory()->create();

        $this->post(route('threads.store'), [
            'title' => $title = 'this is title',
            'body' => $body = 'this is body',
            'channel_id' => $channel->id
        ]);


        $this->get(route('channels.threads.index', $channel->slug))->assertSee($title)->assertSee($body);
    }

    public function test_guest_user_can_not_creat_a_new_forum_threads()
    {
        $channel = create(Channel::class);
        $this->post(route('threads.store', $channel->slug), [
            'title' => $title = 'this is title',
            'body' => $body = 'this is body'
        ])->assertRedirect(route('login'));

        $this->get(route('channels.threads.index', $channel->slug))->assertDontSee($title)->assertDontSee($body);
    }

    public function test_guest_user_can_not_see_create_threads_page()
    {
        $this->get(route('threads.create'))->assertRedirect(route('login'));
    }

    public function test_create_thread_require_title()
    {
        $this->verifyEmail($this->signIn(create(User::class, ['email_verified_at' => null])));
        $this->publishThread(['body' => 'this is body'])->assertSessionHasErrors(['title']);
    }

    public function test_create_thread_require_body()
    {
        $this->verifyEmail($this->signIn(create(User::class, ['email_verified_at' => null])));
        $this->publishThread(['title' => 'this is title'])->assertSessionHasErrors(['body']);
    }

    public function test_user_can_delete_thread()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn(create(User::class, ['email_verified_at' => null]));
        $thread = create(Thread::class, ['user_id' => auth()->id()]);
        $replies = create(Reply::class, ['thread_id' => $thread->id], 10);
        $this->verifyEmail($user);
        $this->deleteJson($thread->destroyThreadPath());

        $replies->each(fn($reply) => $this->assertDatabaseMissing('replies', ['thread_id' => $thread->id, 'id' => $reply->id]));
        $this->assertDatabaseMissing('activities', [
            'subject_type' => Thread::class,
            'subject_id' => $thread->id
        ]);
        $replies->each(function ($reply) {
            $this->assertDatabaseMissing('activities', [
                'subject_type' => Reply::class,
                'subject_id' => $reply->id
            ]);
        });


        $this->assertCount(0, $thread->replies);
    }

    public function test_guest_user_can_not_delete_thread()
    {
        $thread = create(Thread::class);
        $replies = create(Reply::class, ['thread_id' => $thread->id], 10);

        $this->deleteJson(route('threads.destroy', [$thread->slug, $thread->id]))->assertStatus(401);

        $replies->each(fn($reply) => $this->assertDatabaseHas('replies', ['thread_id' => $thread->id, 'id' => $reply->id]));
        $this->assertCount(10, $thread->replies);

    }

    public function test_user_can_not_delete_thread_belong_to_another_user()
    {
        $this->verifyEmail($user = $this->signIn(create(User::class, ['email_verified_at' => null])));
        $thread = create(Thread::class, ['user_id' => $user->id]);

        $anotherThread = create(Thread::class);
        $this->deleteJson(route('threads.destroy', [$anotherThread->id, $anotherThread->slug]))->assertStatus(403);

        Thread::all()->each(fn($thread) => $this->assertDatabaseHas('threads', ['id' => $thread->id]));
        $this->assertCount(2, Thread::all());

    }

    public function test_admin_user_can_delete_any_thread()
    {
        $this->withoutExceptionHandling();
        $this->verifyEmail($admin = $this->signIn(create(User::class, ['email' => 'admin@gmail.com', 'email_verified_at' => null])));
        $thread = create(Thread::class, ['user_id' => $admin->id]);

        $anotherThread = create(Thread::class);
        $this->deleteJson($anotherThread->destroyThreadPath());
        $this->deleteJson($thread->destroyThreadPath());

        Thread::all()->each(fn($thread) => $this->assertDatabaseMissing('threads', ['id' => $thread->id]));
        $this->assertCount(0, Thread::all());
    }

    public function test_can_get_correct_reply_count()
    {
        $this->session(['skipCaptchaValidation' => true]);
        $user = $this->signIn();
        $thread = create(Thread::class);
        self::assertCount(0, $thread->replies);
        self::assertEquals(0, $thread->loadCount('replies as repliesCount')->repliesCount);

        $livewire = Livewire::actingAs($user)->test(\App\Http\Livewire\NewReply::class, ['thread' => $thread]);

        $livewire->set('body', 'test')->call('storeReply');
        self::assertCount(1, $thread->fresh()->replies);
        self::assertEquals(1, $thread->loadCount('replies as repliesCount')->repliesCount);
        sleep(6);

        $livewire->set('body', 'test')->call('storeReply');
        self::assertCount(2, $thread->fresh()->replies);
        self::assertEquals(2, $thread->loadCount('replies as repliesCount')->repliesCount);

        Livewire::actingAs($user)->test(\App\Http\Livewire\Reply::class, ['reply' => $thread->fresh()->replies[0]])->call('deleteReply');
        self::assertCount(1, $thread->fresh()->replies);
        self::assertEquals(1, $thread->loadCount('replies as repliesCount')->repliesCount);
    }

    public function test_can_view_multiple_thread_with_the_same_title()
    {
        $this->verifyEmail($this->signIn(create(User::class, ['email_verified_at' => null])));
        $this->post(route('threads.store', raw(Thread::class, ['title' => 'this is thread'])));
        sleep(6);
        $this->post(route('threads.store', raw(Thread::class, ['title' => 'this is thread'])));

        $this->assertDatabaseHas('threads', ['slug' => 'this-is-thread']);
        $this->assertDatabaseHas('threads', ['slug' => 'this-is-thread-2']);
    }

    public function test_it_use_thread_slug_in_url()
    {
        $this->verifyEmail($this->signIn(create(User::class, ['email_verified_at' => null])));
        $this->post(route('threads.store', raw(Thread::class, ['title' => 'this is thread'])));
        sleep(6);
        $this->post(route('threads.store', raw(Thread::class, ['title' => 'this is thread'])));

        $this->get(route('channels.threads.index'))
            ->assertSee('this-is-thread')
            ->assertSee('this-is-thread-2');
    }

    public function test_can_show_thread_by_slug()
    {
        $this->verifyEmail($this->signIn(create(User::class, ['email_verified_at' => null])));
        $thread = create(Thread::class);
        $this->get($thread->showThreadPath())->assertSee($thread->title);
    }

    public function test_can_update_thread()
    {
        $this->verifyEmail($this->signIn());
        $thread = create(Thread::class, ['user_id' => auth()->id()]);
        $this->assertDatabaseMissing('threads', ['title' => 'update', 'body' => 'update']);
        Livewire::test(ThreadShow::class, ['thread' => $thread, 'channels' => Channel::all()])
           ->set('title', 'update')
           ->set('body', 'update')
           ->call('updateThread')
            ->set('title', 'update 2')
            ->call('updateThread');
        $this->assertDatabaseHas('threads', ['title' => 'update 2', 'body' => 'update']);
    }

    private function publishThread($attributes = [])
    {
        $channel = Channel::factory()->create();

        return $this->post(route('threads.store', $channel->slug), $attributes);
    }
}
