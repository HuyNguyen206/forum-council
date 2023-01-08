<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ThreadUpdateNotifyTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;

    public function test_show_thread_bold_will_create_cache_key()
    {
        $this->signIn();
        $thread = create(Thread::class);
        $this->get($thread->showThreadPath());
        $key = sprintf("users.%s.visits.threads.%s", auth()->id(), $thread->id);
        self::assertNotNull(Cache::get($key));
        self::assertEquals(Cache::get($key), $thread->updated_at);
    }

    public function test_unauthenticate_user_show_thread_bold_will_not_create_cache_key()
    {
        $thread = create(Thread::class);
        $this->get($thread->showThreadPath());
        $key = sprintf("users.%s.visits.threads.%s", auth()->id(), $thread->id);
        self::assertNull(Cache::get($key));
    }

    public function test_can_detect_new_reply_for_thread_since_the_last_time()
    {
        //User x Create thread A
        //User X assert hasNewupdate for thread A is false
        //User y assert hasNewupdate for thread A is true

        //User x go to thread A
        //User x add new reply to thread A
        //User x assert hasNewupdate for thread A is false

        //User y assert hasNewupdate for thread A is true
        //User y go to thread A
        //User y assert hasNewupdate for thread A is false
        //User Y add new reply to thread A
        //User X assert hasNewupdate for thread A is true
        //User y assert hasNewupdate for thread A is false
        $userX = create(User::class);
        $userY= create(User::class);
        $this->signIn($userX);
        $thread = create(Thread::class, ['user_id' => $userX->id]);
        self::assertFalse($thread->hasNewUpdate());

        $this->signIn($userY);
        self::assertTrue($thread->fresh()->hasNewUpdate());

        $this->signIn($userX);
        $this->get($thread->showThreadPath());
        sleep(1);
        create(Reply::class, ['thread_id' => $thread->id, 'user_id' => $userX->id]);
        self::assertFalse($thread->fresh()->hasNewUpdate());

        $this->signIn($userY);
        self::assertTrue($thread->fresh()->hasNewUpdate());
        $this->get($thread->showThreadPath());
        self::assertFalse($thread->fresh()->hasNewUpdate());
        sleep(1);
        create(Reply::class, ['thread_id' => $thread->id, 'user_id' => $userY->id]);
        self::assertFalse($thread->fresh()->hasNewUpdate());

        $this->signIn($userX);
        self::assertTrue($thread->fresh()->hasNewUpdate());
    }
}
