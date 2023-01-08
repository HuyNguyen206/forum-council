<?php

namespace App\Filter;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ThreadFilter extends Filter
{
    protected array $filters = ['by', 'popular', 'noReply'];

    protected function noReply()
    {
        $this->builder->whereDoesntHave('replies');
    }

    protected function by($userName)
    {
        $user = User::query()->where('name', $userName)->first();

        $this->builder->where('user_id', $user->id);
    }

    protected function popular($sortBy = 'desc')
    {
        $this->builder->reorder('repliesCount', $sortBy);
    }

    public static function filter($builder): Builder
    {
        return (new static($builder))->apply();
    }

}
