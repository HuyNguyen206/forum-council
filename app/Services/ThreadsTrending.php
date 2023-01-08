<?php

namespace App\Services;

use App\Models\Thread;
use RedisAlias;

class ThreadsTrending implements RedisKeyInterface
{
    public function get()
    {
        $threadTrendingIdsWithScore = $this->getThreadTrendingIdsWithScore();

        if ($threadTrendingIdsWithScore) {
            $trendingThreads = Thread::whereIn('id', $threadsIds = array_keys($threadTrendingIdsWithScore))->get();
            $trendingThreads = collect($threadsIds)->map(function ($threadId) use ($trendingThreads) {
                return $trendingThreads->find($threadId);
            })->filter();
        } else {
            $trendingThreads = collect([]);
        }

        return $trendingThreads;
    }

    public function getThreadTrendingIdsWithScore()
    {
        return RedisAlias::zrevrange($this->getCacheKey(), 0, 3, ['WITHSCORES' => true]);
    }

    public function push($threadId)
    {
        RedisAlias::zincrby($this->getCacheKey(), 1, $threadId);
    }


    public function getCacheKey()
    {
        return app()->environment('testing') ? 'testing_threads_trending' : 'threads_trending';
    }
}
