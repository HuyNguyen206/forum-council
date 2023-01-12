<?php

namespace Tests\Feature;

use App\Models\Thread;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ThreadTrendingTest extends TestCase
{
    use RefreshRedis;

    public function test_it_can_store_and_sort_trending_threads_correctly_each_time_the_thread_is_viewed()
    {
        $thread = create(Thread::class);
        self::assertEmpty($this->trending->get());
        $this->get($thread->showThreadPath());
        $this->get($thread->showThreadPath());

        $otherThread = create(Thread::class);
        $this->get($otherThread->showThreadPath());
        $this->get($otherThread->showThreadPath());
        $this->get($otherThread->showThreadPath());

        self::assertCount(2, $threadTrendingIdsWithScore = $this->trending->getThreadTrendingIdsWithScore());

        $threadIds = array_keys($threadTrendingIdsWithScore);
        $scores = array_values($threadTrendingIdsWithScore);

        self::assertEquals($threadIds[0], $otherThread->id);
        self::assertEquals($threadIds[1], $thread->id);

        self::assertEquals($scores[0], 3.0);
        self::assertEquals($scores[1], 2.0);
    }

    public function test_can_see_number_of_visit_each_thread()
    {
        $thread = create(Thread::class);
        self::assertEmpty($this->trending->get());
        $this->get($thread->showThreadPath());
        $this->get($thread->showThreadPath());

        $otherThread = create(Thread::class);
        $this->get($otherThread->showThreadPath());
        $this->get($otherThread->showThreadPath());
        $this->get($otherThread->showThreadPath());

        $this->get(route('channels.threads.index'))
            ->assertSee('views:3')
            ->assertSee('views:2');
    }

    public function test_it_thread_can_record_the_visit()
    {
        $thread = create(Thread::class);
        self::assertEquals(0, $thread->visits());
        $this->threadVisits->recordVisits($thread->id);
        self::assertEquals(1, $thread->visits());
    }

}
