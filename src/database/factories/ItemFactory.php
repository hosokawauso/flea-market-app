<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_name' => $this->faker->word(),
            'brand_name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 100000),
            'description' => $this->faker->realText(255),
            'item_img' => 'item_imgs/default.png',
            'condition' => $this->faker->numberBetween(1, 4),
            'is_sold' => false,
        ];
    }
}
