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

    public function placeOrder(PlaceOrderRequest $request) {

        $response = [
            "success" => false,
            "error" => '',
            "message" => "Something went wrong"
        ];

        $products = $request->products;

        $total_qty = collect($products)->sum('quantity');
        if ($total_qty > 0) {
            $order = Order::create(['total_qty' => $total_qty]);

            if ($order) {

                foreach ($products as $key => $each) {
                    $product = Product::where('id', $each['product_id'])->first();
                    if (!empty($product)) {
                        $order_detail = OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => $each['product_id'],
                            'product_name' => $product->name,
                            'qty' => $each['quantity'],
                        ]);

                        if ($product->ingredients) {

                            foreach ($product->ingredients as $ingredient_data) {
                                $ingredient = Ingredient::where('id', $ingredient_data->ingredient_id)->first();
                                if (!empty($ingredient)) {
                                    $consume_qty = $ingredient_data->qty * $each['quantity'];

                                    if ($ingredient->current_stock_level > 0) {
                                        $ingredient->current_stock_level -= $consume_qty;
                                    }

                                    if ($ingredient->current_stock_level < $ingredient->initial_stock_level / 2 && $ingredient->is_low_stock_notified == '0') {
                                        $this->sendLowStockEmail($ingredient);
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

            $response = [
                "success" => true,
                "error" => '',
                "message" => "Order placed successfully"
            ];

        }

        return response()->json($response, $response['success'] ? 200 : 401);

    }

    private function sendLowStockEmail($ingredient) {

        Log::info('email send', ['in' => $ingredient]);

//        Mail::to('test@test.com')->send(new LowStockEmail($ingredient));
    }
}
