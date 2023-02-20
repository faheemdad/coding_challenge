<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $qty = $this->faker->numberBetween(1,10);
        return [
            'name' => $this->faker->name(),
            'qty' => $qty,
            'unit' => 'kg',
            'stock_level' => $qty * 1000,
        ];
    }
}
