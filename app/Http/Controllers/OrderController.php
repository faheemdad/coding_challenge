<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Mail\LowStockEmail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    /**
     * @param PlaceOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(PlaceOrderRequest $request) {

        // Set default response values
        $response = [
            "success" => false,
            "error" => '',
            "message" => "Something went wrong"
        ];

        // Retrieve the products array from the request
        $products = $request->products;

        // Calculate the total quantity of products in the order
        $total_qty = collect($products)->sum('quantity');

        // If the order contains at least one product
        if ($total_qty > 0) {

            // Create a new Order instance with the total quantity
            $order = Order::create(['total_qty' => $total_qty]);

            // If the order was created successfully
            if ($order) {

                // Iterate through each product in the order
                foreach ($products as $key => $each) {

                    // Find the corresponding Product instance in the database
                    $product = Product::where('id', $each['product_id'])->first();

                    // If the product exists in the database
                    if (!empty($product)) {

                        // Create a new OrderDetail instance for the product
                        $order_detail = OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => $each['product_id'],
                            'product_name' => $product->name,
                            'qty' => $each['quantity'],
                        ]);

                        // If the product has associated ingredients
                        if ($product->ingredients) {

                            // Iterate through each ingredient associated with the product
                            foreach ($product->ingredients as $ingredient_data) {

                                // Find the corresponding Ingredient instance in the database
                                $ingredient = Ingredient::where('id', $ingredient_data->ingredient_id)->first();

                                if (!empty($ingredient)) {

                                    // Calculate the amount of the ingredient consumed by the product
                                    $consume_qty = $ingredient_data->qty * $each['quantity'];

                                    // Update the ingredient's stock level based on the amount consumed
                                    if ($ingredient->current_stock_level > 0) {
                                        $ingredient->current_stock_level -= $consume_qty;
                                    }

                                    // If the ingredient's stock level falls below half of the initial stock level which is 50% and has not been notified yet
                                    if ($ingredient->current_stock_level < $ingredient->initial_stock_level / 2 && $ingredient->is_low_stock_notified == '0') {

                                        // Send a low stock notification email for the ingredient
                                        $this->sendLowStockEmail($ingredient);

                                        // Update the ingredient's notification status and notification timestamp
                                        $ingredient->is_low_stock_notified = '1';
                                        $ingredient->is_low_stock_notified_date_time = date('Y-m-d H:i:s');
                                    }

                                    $ingredient->save();
                                }
                            }

                        }

                    }

                }

            }

            // Update the response values to reflect a successful order placement
            $response = [
                "success" => true,
                "error" => '',
                "message" => "Order placed successfully"
            ];

        }

        return response()->json($response, $response['success'] ? 200 : 401);

    }

    /**
     * @param $ingredient
     */
    private function sendLowStockEmail($ingredient) {

//        Log::info('email send', ['in' => $ingredient]);
        Mail::to('test@test.com')->send(new LowStockEmail($ingredient));
    }
}
