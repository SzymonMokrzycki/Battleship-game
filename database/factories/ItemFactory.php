<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $names = ["Upgrade armament", "Increase number canons", "Stronger crew", "Better body and more hp"];
        
        $number = fake()->numberBetween($min = 0, $max = 3);

        $name = $names[$number];

        $price = fake()->numberBetween($min = 100, $max = 5000);

        $property = fake()->numberBetween($min = 1, $max = 100);

        $categories = ['armament', 'cannons', 'strongcrew', 'hp'];

        $category = $categories[$number];

        return [
            'name' => $name,
            'price' => $price,
            'property' => $property,
            'category' => $category
        ];
    }
}
