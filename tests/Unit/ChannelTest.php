<?php

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ChannelTest extends TestCase
{
     use RefreshDatabase, RefreshRedis;

     public function test_a_channel_consist_of_threads()
     {
         $channel = create(Channel::class);
         $channel->threads()->createMany(raw(Thread::class, count: 4));

         $this->assertCount(4, $channel->threads);
     }
}
