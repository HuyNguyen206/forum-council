<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Notification::fake();
        $user = \App\Models\User::factory()->create([
            'name' => 'Huy',
            'name_slug' => 'huy',
            'email' => 'nguyenlehuyuit@gmail.com',
        ]);

        $admin = \App\Models\User::factory()->create([
            'name' => 'admin',
            'name_slug' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        Auth::login($user);
        Channel::factory(10)->create();
        $users = \App\Models\User::factory(10)->create();

        $users->each(function ($user) {
            Thread::factory(rand(1, 2))
                ->create(['user_id' => $user->id, 'channel_id' => Channel::query()->inRandomOrder()->value('id')])
                ->each(function ($thread) {
                    Reply::factory(rand(1, 2))->
                    create([
                        'thread_id' => $thread->id,
                        'user_id' => User::query()->inRandomOrder()->value('id')
                    ]);
                });
        });

        \App\Models\Activities::factory(2)->create(['user_id' => $user->id, 'created_at' =>
            \Illuminate\Support\Carbon::now()->subDay()]);

        \App\Models\Activities::factory(2)->create(['user_id' => $user->id, 'created_at' =>
            \Illuminate\Support\Carbon::now()->subDay(2)]);


    }
}
