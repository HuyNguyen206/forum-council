<?php

namespace Tests\Feature;

use App\Exceptions\SpamReplyException;
use App\Http\Livewire\NewReply;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;
use Tests\Traits\VerifyEmail;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase, RefreshRedis, VerifyEmail;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->session(['skipCaptchaValidation' => true]);
    }

    public function test_authenticate_user_can_participate_in_forum_threads()
    {
        Livewire::actingAs($user = create(User::class))->test(\App\Http\Livewire\NewReply::class, ['thread' =>  $thread = create(Thread::class)])
        ->set(['body' => 'This is a new reply'])->call('storeReply');

        $this->get($thread->showThreadPath())->assertSee('This is a new reply');
    }

    public function test_authenticate_user_can_delete_reply()
    {
        Livewire::actingAs($user = create(User::class))
            ->test(\App\Http\Livewire\Reply::class, ['reply' =>  $reply = create(Reply::class, ['user_id' => $user->id])])
            ->call('deleteReply');

        $this->get($reply->thread->showThreadPath())->assertDontSee($reply->body);
        $this->assertDatabaseMissing('replies', ['user_id' => $user->id, 'id' => $reply->id]);
    }

    public function test_unauthenticate_user_can_not_delete_reply()
    {
        Livewire::test(\App\Http\Livewire\Reply::class, ['reply' =>  $reply = create(Reply::class)])
            ->call('deleteReply')->assertRedirect(route('login'));
        $this->assertDatabaseHas('replies', ['id' => $reply->id]);
    }

    public function test_thread_owner_and_can_delete_only_associate_replies()
    {
        $user = create(User::class);
        $thread = create(Thread::class, ['user_id' => $user->id]);
        $reply = create(Reply::class, ['thread_id' => $thread->id]);
        $otherReply = create(Reply::class);

        Livewire::actingAs($user)->test(\App\Http\Livewire\Reply::class, ['reply' =>  $reply])
            ->call('deleteReply');
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        Livewire::actingAs($user)->test(\App\Http\Livewire\Reply::class, ['reply' =>  $otherReply])
            ->call('deleteReply')->assertStatus(403);;

        $this->assertDatabaseHas('replies', ['id' => $otherReply->id]);
    }

    public function test_reply_owner_and_can_delete_only_his_own_replies()
    {
        $user = create(User::class);
        $reply = create(Reply::class, ['user_id' => $user->id]);
        $otherReply = create(Reply::class);

        Livewire::actingAs($user)->test(\App\Http\Livewire\Reply::class, ['reply' =>  $reply])
            ->call('deleteReply');
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        Livewire::actingAs($user)->test(\App\Http\Livewire\Reply::class, ['reply' =>  $otherReply])
            ->call('deleteReply')->assertStatus(403);
        $this->assertDatabaseHas('replies', ['id' => $otherReply->id]);
    }

    public function test_unauthenticate_user_can_not_add_reply()
    {
        Livewire::test(\App\Http\Livewire\NewReply::class, ['body' =>  'test', 'thread' => create(Thread::class)])
            ->call('storeReply')->assertRedirect(route('login'));
        $this->assertEquals(0, Reply::count());

    }

    public function test_create_a_reply_require_body()
    {
        $this->withoutExceptionHandling();
        $user = create(User::class);
        $thread = create(Thread::class);

        Livewire::actingAs($user)->test(\App\Http\Livewire\NewReply::class, ['thread' => $thread])
            ->call('storeReply');

        $this->assertCount(0, Reply::all());
    }

    public function test_user_can_update_reply()
    {
        Livewire::actingAs($user = create(User::class))
            ->test(\App\Http\Livewire\Reply::class, ['reply' => $reply = create(Reply::class, ['user_id' => $user->id])])
            ->set('body', 'update')
            ->call('updateReply');

        $this->assertDatabaseHas('replies', ['user_id' => $user->id, 'body' => 'update']);
    }

    public function test_it_can_detect_spam_reply()
    {
        $user = $this->signIn();
        $thread = create(Thread::class, ['user_id' => $user->id]);
        Livewire::actingAs($user = create(User::class))->test(NewReply::class, ['thread' => $thread])
            ->set('body', 'Yahoo customer support')
            ->call('storeReply')
            ->assertHasErrors('body');

    }

    public function test_it_can_detect_thread_with_spam()
    {
        $this->verifyEmail($this->signIn());

        $this->post(route('threads.store'), ['title' =>'aaaaaaaaa', 'body' => 'valid one'])->assertSessionHasErrors('title');
        $this->post(route('threads.store'), ['title' =>'valid one', 'body' => 'aaaaaaaaaa'])->assertSessionHasErrors('body');

        $this->post(route('threads.store'), ['title' =>'Yahoo customer support', 'body' => 'valid one'])->assertSessionHasErrors('title');
        $this->post(route('threads.store'), ['title' =>'valid one', 'body' => 'Yahoo customer support'])->assertSessionHasErrors('body');
    }

    public function test_it_can_detect_spam_reply_in_case_insensitive()
    {
        $user = $this->signIn();
        $thread = create(Thread::class, ['user_id' => $user->id]);
        Livewire::actingAs($user)->test(NewReply::class, ['thread' => $thread])
            ->set('body', 'yahoo customer support')
            ->call('storeReply')
        ->assertHasErrors('body');
    }

    public function test_it_can_detect_spam_reply_in_case_update_the_reply()
    {
        $user = $this->signIn();
        Livewire::actingAs($user)->test(\App\Http\Livewire\Reply::class, ['reply' => $reply = create(Reply::class)])
            ->set('body', 'aaaaaaaa')
            ->call('updateReply')
            ->assertHasErrors('body');
    }

    public function test_it_require_valid_captcha_token()
    {
        $this->session(['skipCaptchaValidation' => false]);

        $this->withoutExceptionHandling();
        $thread = create(Thread::class);
        Livewire::actingAs(create(User::class))->test(NewReply::class, ['thread' => $thread])
            ->set('body', 'this is reply')
            ->call('storeReply')
            ->assertHasErrors('captchaToken')
            ->set('captchaToken', 'this is invalid token')
            ->call('storeReply')
            ->assertHasErrors('captchaToken');
    }

}
