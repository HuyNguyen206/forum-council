<?php

namespace App\Services;

use RedisAlias;

class ThreadsVisits implements RedisKeyInterface
{
    public function getCacheKey($threadId = null)
    {
        return "threads_{$threadId}_visits";
    }

    public function recordVisits($threadId)
    {
        RedisAlias::incr($this->getCacheKey($threadId));
    }

    public function visits($threadId)
    {
        return (int) RedisAlias::get($this->getCacheKey($threadId));
    }
}
