<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ReadThreadTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;

    public function test_the_user_can_view_single_threads()
    {
        $thread = Thread::factory()->create();

        $response = $this->get($thread->showThreadPath());

        $response->assertStatus(200)->assertSee($thread->title);
    }

   public function test_the_user_can_view_replise_that_associate_with_threads()
    {
        $thread = Thread::factory()->create();

        $thread->replies()->createMany(Reply::factory(5)->raw());

        $response = $this->get($thread->showThreadPath());
        $replies = Reply::all();
        $response->assertStatus(200);
        $replies->each(function ($reply) use ($response){
            $response->assertSee($reply->body);
        });
    }

   public function test_user_can_view_threads_belong_to_a_channel()
   {
       $otherThreads = create(Thread::class, count: 2);
       $channel = create(Channel::class);

       $threads = $channel->threads()->createMany(raw(Thread::class, count: 4));

       $response = $this->get(route('channels.threads.index', $channel->slug));

       foreach ($threads as $thread) {
           $response->assertSee($thread->title)->assertSee($thread->body);
       }

       foreach ($otherThreads as $otherThread) {
           $response->assertDontSee($otherThread->title)->assertDontSee($otherThread->body);
       }

   }

   public function test_a_user_can_filter_threads_by_any_username()
   {
       $this->signIn($huy = create(User::class, ['name' => 'huy']));
       $threadByHuy = create(Thread::class, ['user_id' => $huy->id]);

       $otherThread = create(Thread::class);

       $this->get(route('channels.threads.index', ['by' => $huy->name]))
           ->assertSee($threadByHuy->title)
           ->assertDontSee($otherThread->title);
   }

   public function test_user_can_filter_thread_by_popularity_desc()
   {
       $this->signIn($huy = create(User::class, ['name' => 'huy']));
       $thread1 = create(Thread::class, ['title' => 'first', 'created_at' => Carbon::now()]);
       $thread1->replies()->createMany(raw(Reply::class,  count: 1));

       $thread2 = create(Thread::class, ['title' => 'second','created_at' => Carbon::now()->subMinute()]);
       $thread2->replies()->createMany(raw(Reply::class, count: 4));

       $this->get(route('channels.threads.index').'?popular')
           ->assertSeeInOrder([ $thread2->title, $thread1->title]);
   }


   public function test_user_can_filter_thread_by_popularity_asc()
   {
       $this->withoutExceptionHandling();
       $this->signIn($huy = create(User::class, ['name' => 'huy']));
       $thread1 = create(Thread::class);
       create(Reply::class, ['thread_id' => $thread1->id], 1);

       $thread2 = create(Thread::class);
       create(Reply::class, ['thread_id' => $thread2->id], 2);

       $thread3 = create(Thread::class);
       create(Reply::class, ['thread_id' => $thread3->id], 3);

       $this->get(route('channels.threads.index').'?popular=asc')
           ->assertSeeInOrder([$thread1->title, $thread2->title, $thread3->title]);
   }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_user_can_browse_threads()
    {
        $this->withoutExceptionHandling();
        $channel = create(Channel::class);
        create(Thread::class, ['channel_id' => $channel->id], 5);

        $response = $this->get(route('channels.threads.index', $channel->slug));

        $response->assertStatus(200)->assertSee(Thread::query()->inRandomOrder()->first()->value('title'));

    }

    public function test_user_can_filter_thread_without_reply()
    {
        $this->signIn($huy = create(User::class, ['name' => 'huy']));
        $threadsWithoutReply = create(Thread::class, count: 2);
        $threadsWithReply = create(Thread::class, count: 2);

        $threadsWithReply->each(function ($thread){
            create(Reply::class, ['thread_id' => $thread->id], rand(1, 4));
        });

        $response = $this->get(route('channels.threads.index').'?noReply');
        $threadsWithReply->each(function ($thread) use($response) {
            $response->assertDontSee($thread->title);
        });
        $threadsWithoutReply->each(function ($thread) use($response) {
            $response->assertSee($thread->title);
        });
    }
}
