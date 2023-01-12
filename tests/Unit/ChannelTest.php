<?php

namespace Tests\Unit;

use App\Http\Livewire\NewChannel;
use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\RefreshRedis;

class ChannelTest extends TestCase
{
    use RefreshRedis;

    public function test_a_channel_consist_of_threads()
    {
        $channel = create(Channel::class);
        $channel->threads()->createMany(raw(Thread::class, count: 4));

        $this->assertCount(4, $channel->threads);
    }

    public function test_only_admin_can_create_channel_in_livewire_component()
    {
        Livewire::actingAs(create(User::class, ['email' => 'admin@gmail.com']))->test(NewChannel::class)->set('name', 'test-component')
            ->call('storeNewChannel');

        $this->assertDatabaseHas('channels', ['name' => 'test-component']);
    }

    public function test_only_admin_can_access_route_create_channel()
    {
        $this->get(route('channels.create'))->assertRedirectToRoute('login');

        $this->actingAs(create(User::class, ['email' => 'non-admin@gmail.com']))
            ->get(route('channels.create'))->assertStatus(403);

        $this->actingAs(create(User::class, ['email' => 'admin@gmail.com']))
            ->get(route('channels.create'))->assertStatus(200);
    }

    public function test_non_admin_user_can_not_create_channel_in_livewire_component()
    {
        Livewire::actingAs(create(User::class, ['email' => 'non-admin@gmail.com']))->test(NewChannel::class)->set('name', 'test-component')
            ->call('storeNewChannel')
            ->assertStatus(403);

        $this->assertDatabaseMissing('channels', ['name' => 'test-component']);
    }

    public function test_admin_can_archive_channel()
    {
        $this->withoutExceptionHandling();
        $this->signInAdmin();
        $channel = create(Channel::class, ['name' => $name = 'This is channel name']);
        self::assertFalse($channel->is_archive);
        $this->patch(route('channels.toggle-archive', $channel->slug));
        self::assertTrue($channel->fresh()->is_archive);
    }
}
