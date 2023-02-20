<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductIngredientFactory extends Factory
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
            'product_id' => Product::all()->random(),
            'ingredient_id' => $this->faker->name(),
            'qty' => $qty,
            'unit' => 'kg',
            'stock_level' => $qty * 1000,
        ];
    }
}
