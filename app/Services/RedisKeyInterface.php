<?php

namespace App\Services;

interface RedisKeyInterface
{
    public function getCacheKey();
}
