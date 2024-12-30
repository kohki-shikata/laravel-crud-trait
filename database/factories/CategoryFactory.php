<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => $this->faker->word,
            'description' => $this->faker->sentence,
        ];
    }
}
