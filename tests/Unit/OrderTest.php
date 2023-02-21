<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use Tests\TestCase;
class OrderTest extends TestCase
{
    protected $data = [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 2,
            ],
            [
                'product_id' => 2,
                'quantity' => 1,
            ],
        ],
    ];
    /**
     * With empty payload.
     */
    public function test_place_an_order_with_empty_payload()
    {
        $payload = [];

        $response = $this->post('/api/place-order', $payload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'products',
        ]);

        $this->assertDatabaseMissing('orders', [
            'total_qty' => 0,
        ]);

        $this->assertDatabaseMissing('order_details', [
            'order_id' => 1,
            'product_id' => 1,
            'product_name' => 'Burger',
            'qty' => 2,
        ]);

    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_place_an_order_successfully()
    {
        $payload = $this->data;

        $response = $this->post('/api/place-order', $payload);

        $this->assertTrue(true);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order placed successfully',
            ]);
    }


    public function test_place_order_with_valid_payload()
    {
        $this->withoutExceptionHandling();
        $burger = Product::create(['name' => 'Burger']);

        $beef = Ingredient::create(['name' => 'Beef', 'qty' => 20, 'initial_stock_level' => 20000, 'current_stock_level' => 20000]);
        $cheese = Ingredient::create(['name' => 'Cheese','qty' => 5, 'initial_stock_level' => 5000, 'current_stock_level' => 5000]);
        $onion = Ingredient::create(['name' => 'Onion','qty' => 1, 'initial_stock_level' => 1000, 'current_stock_level' => 1000]);


        ProductIngredient::create(['product_id' => $burger->id,'qty' => 150, 'ingredient_id' => $beef->id]);
        ProductIngredient::create(['product_id' => $burger->id,'qty' => 150, 'ingredient_id' => $cheese->id]);
        ProductIngredient::create(['product_id' => $burger->id,'qty' => 150, 'ingredient_id' => $onion->id]);

        $payload = [
            'products' => [
                ['product_id' => $burger->id, 'quantity' => 2]
            ]
        ];

        $response = $this->json('POST', '/api/place-order', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'error' => '',
                'message' => 'Order placed successfully',
            ]);

        $this->assertDatabaseHas('orders', [
            'total_qty' => 2,
        ]);

        $this->assertDatabaseHas('order_details', [
            'order_id' => 1,
            'product_id' => $burger->id,
            'product_name' => 'Burger',
            'qty' => 2,
        ]);

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Beef',
            'initial_stock_level' => 20000,
            'current_stock_level' => 19700,
            'is_low_stock_notified' => '0',
        ]);

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Cheese',
            'initial_stock_level' => 5000,
            'current_stock_level' => 4700,
            'is_low_stock_notified' => '0',
        ]);

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Onion',
            'initial_stock_level' => 1000,
            'current_stock_level' => 700,
            'is_low_stock_notified' => '0',
        ]);

    }

    public function test_place_an_order_with_zero_quantity()
    {
        $payload = [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 0,
                ],
                [
                    'product_id' => 2,
                    'quantity' => 0,
                ],
            ],
        ];

        $response = $this->post('/api/place-order', $payload);

        $this->assertTrue(true);

        $response->assertStatus(422);

    }

    public function test_place_order_with_invalid_payload() {

        $payload = [
            'products' => [
                ['product_id' => 123, 'quantity' => 2]
            ]
        ];

        $response = $this->json('POST', '/api/place-order', $payload);

        $this->assertTrue(true);

        $response->assertStatus(401);

    }
}
