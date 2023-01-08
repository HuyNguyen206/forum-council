<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ProfileTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;

    public function test_a_user_has_a_profile()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $this->get(route('users.profile', $user->name_slug))->assertSee($user->email);
    }

    public function test_profile_display_activity_by_associate_user()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();

        $user->threads()->createMany(raw(Thread::class, count: 5));

        $response = $this->get(route('users.profile', $user->name_slug));

        $user->activities->each(function ($activity) use($response) {
            $subject = $activity->subject;
            $response->assertSee($subject instanceof Thread ? $subject->title : $subject->thread->title);
        });

    }
}
