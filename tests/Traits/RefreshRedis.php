<?php

namespace Tests\Traits;

use App\Services\RedisManager;
use App\Services\ThreadsTrending;
use App\Services\ThreadsVisits;

trait RefreshRedis
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->trending  = app(ThreadsTrending::class);
        $this->threadVisits = app(ThreadsVisits::class);

        RedisManager::clean();
    }
}