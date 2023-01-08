<?php


use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ReplyTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_it_has_an_owner()
    {
      $reply = Reply::factory()->create();
      self::assertInstanceOf(User::class, $reply->user);
    }

    public function test_it_can_fetch_mentioned_user_name_from_body()
    {
        $reply = create(Reply::class, ['body' => '@nhung hi, @huy hello how are you guy! @nguyen-nam test']);

        $mentionUserNames = $reply->getMentionUserNames();

        self::assertContains('nhung', $mentionUserNames);
        self::assertContains('huy', $mentionUserNames);
        self::assertContains('nguyen-nam', $mentionUserNames);
    }

    public function test_wrap_user_name_inside_anchor_tag()
    {
        $nam = create(User::class, ['name' => 'nguyen name', 'name_slug' => 'nguyen-nam']);
        $huy = create(User::class, ['name' => 'huy', 'name_slug' => 'huy']);
        $nhung = create(User::class, ['name' => 'nhung', 'name_slug' => 'nhung']);
        $reply = create(Reply::class, ['body' => '@nhung hi, @huy hello how are you guy! @nguyen-nam test']);

        self::assertTrue(str_contains($reply->body, $nhung->generateProfileLink()));
        self::assertTrue(str_contains($reply->body, $nam->generateProfileLink()));
        self::assertTrue(str_contains($reply->body, $huy->generateProfileLink()));

    }
}
