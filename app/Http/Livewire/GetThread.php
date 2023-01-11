<?php

namespace App\Http\Livewire;

use App\Filter\ThreadFilter;
use App\Models\Channel;
use App\Models\Thread;

trait GetThread
{
    /**
     * @param Channel|null $channel
     * @return mixed
     */
    protected function getThreads(?Channel $channel = null)
    {
        if (property_exists($this, 'q')) {
            return trim($this->q) ? Thread::search($this->q)->take(10)->get() : collect([]);
        }

        if ($channel) {
            $builder = $channel->threads()->getQuery();
        } else {
            $builder = Thread::query();
        }

        $builder = $builder->withCount('replies as repliesCount')->with('user')->orderByDesc('is_pin')->latest('updated_at');

        return ThreadFilter::filter($builder)->paginate(5, pageName:'page-thread')->withQueryString();
    }
}
