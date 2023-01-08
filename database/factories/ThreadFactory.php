<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $title = $this->faker->words(3, true),
            'body' => $this->faker->paragraphs(2, true),
            'user_id' => fn() => User::factory(),
            'channel_id' => fn() => Channel::factory(),
            'slug' => Str::slug($title),
            'is_lock' => 0
        ];
    }
}
