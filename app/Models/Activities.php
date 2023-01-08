<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Activities extends Model
{
    use HasFactory;

    public function subject()
    {
        return $this->morphTo();
    }

    public function getThreadId()
    {
        if ($this->subject instanceof Thread) {
            return $this->subject_id;
        }

        if ($this->subject instanceof Reply) {
            return $this->subject->thread_id;
        }

        return Thread::find(Reply::find($this->subject->favorite_id)->thread_id)->id;
    }
}
