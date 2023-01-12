<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class RecordActivityTest extends TestCase
{
     use RefreshRedis;

     public function test_create_thread_also_record_activity()
     {
        $thread = create(Thread::class);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'user_id' => $thread->user_id,
            'type' => 'thread_created'
        ]);

         self::assertDatabaseHas('activities', ['subject_type' => Thread::class, 'subject_id' => $thread->id]);
     }

     public function test_create_reply_also_record_activity()
     {
        $reply = create(Reply::class);

        $this->assertDatabaseHas('activities', [
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'user_id' => $reply->user_id,
            'type' => 'reply_created'
        ]);

        self::assertDatabaseHas('activities', ['subject_type' => Reply::class, 'subject_id' => $reply->id]);
     }


}
