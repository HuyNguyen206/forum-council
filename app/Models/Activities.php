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

    public function getThread()
    {
        if ($this->subject instanceof Thread) {
            return $this->subject;
        }

        if ($this->subject instanceof Reply) {
            return $this->subject->thread;
        }

        return Thread::find(Reply::find($this->subject->favorite_id)->thread_id);
    }

    public function getThreadId()
    {
        return $this->getThread()->id;
    }

    public function getThreadSlug()
    {
        return $this->getThread()->slug;
    }
}
