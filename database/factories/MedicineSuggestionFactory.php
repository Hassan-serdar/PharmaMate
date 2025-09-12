<?php

namespace Database\Factories;

use App\Enums\MedicineTypeEnum;
use App\Enums\SuggestionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MedicineSuggestion;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicineSuggestion>
 */
class MedicineSuggestionFactory extends Factory
{
    protected $model = MedicineSuggestion::class;

    public function definition(): array
    {
        return [
            'pharmacist_id' => User::factory(),
            'name' => $this->faker->unique()->word, 
            'active_ingredient' => $this->faker->word,
            'dosage' => $this->faker->word,
            'type' => $this->faker->randomElement(MedicineTypeEnum::cases())->value, 
            'status' => SuggestionStatusEnum::PENDING->value, 
        ];
    }
}
