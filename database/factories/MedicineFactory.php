<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\MedicineTypeEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $commonNames = ['Panadol', 'Amoxil', 'Augmentin', 'Voltaren', 'Sovaldi', 'Concor', 'Cataflam', 'Zithromax'];
        $activeIngredients = ['Paracetamol', 'Amoxicillin', 'Clavulanic Acid', 'Diclofenac', 'Sofosbuvir', 'Bisoprolol', 'Ibuprofen'];

        return [
            'name' => fake()->randomElement($commonNames) . ' ' . fake()->word(),
            'active_ingredient' => fake()->randomElement($activeIngredients),
            'dosage' => fake()->randomElement(['250 mg', '500 mg', '1 g', '100 ml']),
            'type' => fake()->randomElement(MedicineTypeEnum::cases()),
            'image_path' => null, // 
            'description' => fake()->paragraph(2),
            'manufacturer' => fake()->company(),
        ];
    }
}
