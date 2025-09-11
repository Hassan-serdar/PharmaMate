<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Enums\FeedbackTypeEnum;
use App\Enums\FeedbackStatusEnum;
use App\Enums\FeedbackPriorityEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(FeedbackTypeEnum::cases()),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => FeedbackStatusEnum::NEW,
            'priority' => FeedbackPriorityEnum::LOW,
            'assigned_to_user_id' => null,
        ];
    }
}
