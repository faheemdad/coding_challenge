<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $products = ["Burger", "Pizza"];
        $ingredients = [
            ["Onion", 10, "kg", 10000, 10000],
            ["Potato", 12, "kg", 12000, 12000],
            ["Beef", 6, "kg", 6000, 6000],
            ["Chicken", 5, "kg", 5000, 5000],
        ];

        foreach ($products as $each) {
            $obj = Product::firstOrNew(["name" => $each]);
            $obj->save();
        }

        foreach ($ingredients as $each) {
            $obj = Ingredient::firstOrNew([
                "name" => $each[0],
                "qty" => $each[1],
                "unit" => $each[2],
                "initial_stock_level" => $each[3],
                "current_stock_level" => $each[4],
            ]);
            $obj->save();
        }

        $product_ingredients = [
            [1, 1, 300, 'g'],
            [1, 2, 200, 'g'],
            [1, 3, 100, 'g'],

            [2, 1, 700, 'g'],
            [2, 2, 500, 'g'],
            [2, 3, 400, 'g'],
        ];

        foreach ($product_ingredients as $each) {
            $obj = ProductIngredient::firstOrNew([
                "product_id" => $each[0],
                "ingredient_id" => $each[1],
                "qty" => $each[2],
                "unit" => $each[3],
            ]);
            $obj->save();
        }

    }
}
