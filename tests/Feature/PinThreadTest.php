<?php

namespace Tests\Feature;

use App\Http\Livewire\ThreadArticle;
use App\Http\Livewire\ThreadIndex;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;
use Tests\Traits\VerifyEmail;

class PinThreadTest extends TestCase
{
    use RefreshDatabase, RefreshRedis, VerifyEmail;

    public function test_pinned_thread_come_first()
    {
        $threads = create(Thread::class, [], 3);
        $titles = $threads->pluck('title');

        Livewire::test(ThreadIndex::class)->assertSeeTextInOrder([$titles[0], $titles[1], $titles[2]]);

        Livewire::test(ThreadArticle::class, ['thread' => $threads->last()])->call('pinThread');

        Livewire::test(ThreadIndex::class)->assertSeeTextInOrder([$titles[2], $titles[0], $titles[1]]);
    }
}
