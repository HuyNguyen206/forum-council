<?php

namespace Tests\Feature;

use App\Http\Livewire\SearchThread;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;
use Tests\Traits\VerifyEmail;

class SearchTest extends TestCase
{
    use RefreshDatabase, RefreshRedis, VerifyEmail;

    public function test_user_can_search_thread()
    {
        config(['scout.driver' => 'algolia']);

        create(Thread::class, ['title' => 'test'], 2);
        create(Thread::class, ['title' => 'where is huy ta?'], 2);
        sleep(2);

        Livewire::test(SearchThread::class)->set('q', 'huy')
            ->call('search')
            ->assertSee('where is huy ta?');

        Thread::query()->latest()->take(4)->unsearchable();
    }
}