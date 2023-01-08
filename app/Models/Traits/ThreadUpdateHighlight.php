<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait ThreadUpdateHighlight
{

    public function cacheKey()
    {
        Cache::forever($this->createCacheKey(), $this->updated_at);
    }

    public function cacheThreadIgnoreUser($replierId)
    {
        if (auth()->guest()) return;

        $replierIds = Cache::get($key = $this->createThreadIgnoreUserCacheKey());

        if (!$replierIds) {
            return Cache::forever($key, [$replierId]);
        }

        if (!in_array($replierId, $replierIds)) {
            $replierIds = [$replierId];
            Cache::forever($key, $replierIds);
        }
    }

    public function createCacheKey()
    {
        return sprintf("users.%s.visits.threads.%s", auth()->id(), $this->id);
    }

    /**
     * @return string
     */
    public function createThreadIgnoreUserCacheKey(): string
    {
        return sprintf("threads.%s.ignore.users", $this->id);
    }

    public function hasNewUpdate()
    {
        if (auth()->guest()) {
            return false;
        }

        $cacheKeyValue = cache($this->createCacheKey());
        if (!$cacheKeyValue) {
            return true;
        }
        $ignoreUserIds = cache($this->createThreadIgnoreUserCacheKey());
        $isAcceptNewReplyAsNewUpdate = $ignoreUserIds ? !in_array(auth()->id(), $ignoreUserIds) : true;
//        dd($ignoreUserIds, $isAcceptNewReplyAsNewUpdate,  $this->updated_at > $cacheKeyValue);
        return $isAcceptNewReplyAsNewUpdate && $this->updated_at > $cacheKeyValue;
    }
}
