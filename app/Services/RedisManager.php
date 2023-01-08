<?php

namespace App\Services;

class RedisManager
{
    public static function clean()
    {
        \RedisAlias::flushdb();
    }
}
