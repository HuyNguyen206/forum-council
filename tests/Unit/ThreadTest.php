<?php

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ThreadTest extends TestCase
{
     use RefreshRedis;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_thread_has_replies()
    {
       $thread = Thread::factory()->create();
       Reply::factory(4)->create(['thread_id' => $thread->id]);

       self::assertCount(4, $thread->replies);
    }

    public function test_thread_has_creator()
    {
        $thread = Thread::factory()->create();
        self::assertInstanceOf(User::class, $thread->user);
    }

    public function test_thread_belong_to_channel()
    {
       $thread = create(Thread::class);
       self::assertInstanceOf(Channel::class, $thread->channel);
    }

    public function test_a_thread_can_be_subscribed_to()
    {
        $this->withoutExceptionHandling();
        $thread = create(Thread::class);
        $user = $this->signIn();
        $user->subscribe($thread);

        $this->assertCount(1, $user->subcribedThreads);
    }

    public function test_a_thread_can_be_unsubscribed_to()
    {
        $this->withoutExceptionHandling();
        $thread = create(Thread::class);
        $user = $this->signIn();
        $user->subscribe($thread);
        $user->unSubscribe($thread);

        $this->assertCount(0, $user->subcribedThreads);
    }

    public function test_create_thread_generate_slug_from_title()
    {
        $thread = create(Thread::class, ['title' => 'this is thread']);
        self::assertEquals('this-is-thread', $thread->slug);
    }

    public function test_it_can_generate_unique_slug_in_all_cases()
    {
        for ($i = 1; $i <= 3; $i++) {
            $title = fake()->words(2, true);
            for ($j = 1; $j <= 101; $j++) {
                $thread = create(Thread::class, ['title' => $title]);
                $slug = Str::slug($title);
                $evaluatedSlug = $j === 1 ? $slug : "$slug-$j";
                self::assertEquals($thread->slug, $evaluatedSlug);
            }
        }
        $thread = create(Thread::class, ['title' => 'this is thread']);
        self::assertEquals($thread->slug, "this-is-thread");

        $thread2 = create(Thread::class, ['title' => 'this is thread']);
        self::assertEquals($thread2->slug, "this-is-thread-2");

        $thread3 = create(Thread::class, ['title' => 'this is thread']);
        self::assertEquals($thread3->slug, "this-is-thread-3");

        $thread4 = create(Thread::class, ['title' => 'this is thread']);
        self::assertEquals($thread4->slug, "this-is-thread-4");
    }

    public function test_create_new_thread_after_delete_second_thread_after_create_three_thread_with_same_title()
    {
      create(Thread::class, ['title' => 'this is thread'], 3);
      Thread::where('slug', 'this-is-thread-2')->delete();
      $thread = create(Thread::class, ['title' => 'this is thread']);
      self::assertEquals($thread->fresh()->slug, 'this-is-thread-2');
    }

    public function test_update_thread_title_also_update_slug()
    {
        $thread = create(Thread::class);
        $thread->update(['title' => 'this is updated title']);
        self::assertEquals($thread->slug, 'this-is-updated-title');
    }

    public function test_block_create_thread_with_archive_channel()
    {
        $archiveChannel = create(Channel::class, ['is_archive' => true]);

        $this->actingAs($user = create(User::class))->post(route('threads.store'), [
            'channel_id' => $archiveChannel->id,
            'title' => 'test',
            'body' => 'test'
        ])->assertSessionHasErrors('channel_id');

        $this->assertDatabaseMissing('threads', [
            'user_id' => $user->id,
            'title' => 'test',
            'body' => 'test',
            'channel_id' => $archiveChannel->id
        ]);
    }
}
