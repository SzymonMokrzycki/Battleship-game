<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ShipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = fake()->streetName();
        $types =[
            "heavycruiser_fourmasted",
            "heavycruiser_threemasted",
            "frigate_fourmasted",
            "frigate_threemasted",
            "corvette_fourmasted",
            "corvette_threemasted",
            "cruiser_fourmasted",
            "cruiser_threemasted",
            "destroyer_fourmasted",
            "destroyer_threemasted",
            "bark_twomasted",
            "bark_singlemasted",
            "galeon_twomasted",
            "galeon_singlemasted",
            "gunboat_twomasted",
            "gunboat_singlemasted",
            "carawell_twomasted",
            "carawell_singlemasted",
            "corvettelight_twomasted",
            "corvettelight_singlemasted",
        ]; 
        
        $number = fake()->numberBetween($min = 0, $max = count($types)-1);

        $type = $types[$number];

        $numbercanon = fake()->numberBetween($min = 5, $max = 30);

        $armament = fake()->numberBetween($min = 100, $max = 700);

        $strongcrew = fake()->numberBetween($min = 1, $max = 40);
        
        $damage = $armament + $numbercanon + $strongcrew * 0.5;

        $hp = fake()->numberBetween($min = 100, $max = 5000);

        $price = fake()->numberBetween($min = 1000, $max = 20000);
        
        return [
            'name' => $name,
            'type' => $type,
            'number_of_canons' => $numbercanon,
            'strong_crew' => $strongcrew,
            'damage_canons' => $damage,
            'armament' => $armament,
            'hp' => $hp,
            'price' => $price
        ];
    }
}
