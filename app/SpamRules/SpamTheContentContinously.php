<?php

namespace App\SpamRules;

use Carbon\Carbon;

class SpamTheContentContinously
{
    public function detect($value, $model, $attribute)
    {
        $isInvalid = true;
        $key = "$model.$attribute.timeTrack";
        $timeTrack = session()->get($key);

        if ($this->haveValidReponseInTime($timeTrack)) {
            $isInvalid = false;
        }

        if (! $isInvalid) {
            session()->put($key,  now());
        }

        return $isInvalid;
    }

    /**
     * @param mixed $timeTrack
     * @return bool
     */
    protected function haveValidReponseInTime(mixed $timeTrack): bool
    {
        return $timeTrack === null || ($timeTrack && Carbon::instance(now())->diffInSeconds($timeTrack) > 2);
    }
}
