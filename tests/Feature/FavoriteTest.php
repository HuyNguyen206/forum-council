<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class FavoriteTest extends TestCase
{
    use RefreshDatabase, RefreshRedis;

    public function test_guest_user_can_not_favorite_any_reply()
    {
        $reply = create(Reply::class);
        Livewire::test(\App\Http\Livewire\Reply::class, ['reply' => $reply])
            ->call('toggleFavoriteReply')
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('favorites', ['favorite_type' => 'App\Models\Reply', 'favorite_id' => $reply->id]);
    }

    public function test_user_can_toggle_favorite_reply()
    {
        $this->withoutExceptionHandling();
        $reply = create(Reply::class);
        $livewire = Livewire::actingAs($user = create(User::class))->test(\App\Http\Livewire\Reply::class, ['reply' => $reply])
        ->call('toggleFavoriteReply');
        self::assertCount(1, $user->favoriteReplies);
        $livewire->call('toggleFavoriteReply');
        self::assertCount(0, Favorite::all());
        self::assertCount(0, $user->refresh()->favoriteReplies);

    }
}
