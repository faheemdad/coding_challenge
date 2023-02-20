<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function placeOrder(PlaceOrderRequest $request) {

        $products = $request->products;

        return response()->json([
            "success" => true,
            "error" => '',
            "message" => "Order placed successfully.",
        ]);

    }
}
