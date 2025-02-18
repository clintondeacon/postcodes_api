<?php

namespace Database\Factories;

use App\Models\StoreType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'lat' => $this->faker->latitude(),
            'long' => $this->faker->longitude(),
            'is_open' => $this->faker->boolean(),
            'store_type_id' => StoreType::inRandomOrder()->first() ?? StoreType::factory(),
            'max_delivery_distance' => $this->faker->randomFloat(2, 1, 50),
        ];
    }
}
