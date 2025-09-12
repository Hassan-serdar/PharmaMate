<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\PharmacyStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pharmacy>
 */
class PharmacyFactory extends Factory
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
            'name' => 'Pharmacy ' . fake()->lastName(),
            'phone_number' => fake()->unique()->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'latitude' => fake()->latitude(33.4, 33.6),
            'longitude' => fake()->longitude(36.2, 36.4),
            'opening_time' => '09:00',
            'closing_time' => '23:00',
            'status' => PharmacyStatusEnum::ONLINE,
        ];
    }
}
