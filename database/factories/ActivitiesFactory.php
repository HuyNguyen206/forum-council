<?php

namespace Database\Factories;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ActivitiesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $subjectFactory = Arr::random([Thread::factory(), Reply::factory()]);
        $subject = $subjectFactory->create();

        return [
            'user_id' => fn() => User::factory(),
            'subject_type' => $className = get_class($subject),
            'subject_id' => $subject->id,
            'type' => strtolower(class_basename($subject)).'_created'
        ];
    }
}
