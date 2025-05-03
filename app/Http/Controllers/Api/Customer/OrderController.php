<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController
{
    public function getOrders()
    {
        $orders = Order::with('customer' , 'products')->where('customer_id', Auth::user()->id)->paginate(10);
        $data = $orders->toArray();

        $data['data'] = $orders->map(fn($order) => [
            'id' => $order->id,
            'total_price' => $order->total_price,
            'quantity' => $order->quantity,
            'status' => $order->status,
            'created_at' => $order->created_at,
            'products' => $order->products->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
            ])
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function getOrder($orderId)
    {
        $order = Order::with( 'products')->where('customer_id', Auth::user()->id)->find($orderId);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ], 200);
    }

    public function createOrder(Request $request)
    {
        $fields = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'customer_id' => Auth::user()->id,
            'total_price' => 0,
            'quantity' => 0,
            'status' => 'pending',
        ]);

        $productData = [];
        foreach ($fields['products'] as $product) {
            $productData[$product['id']] = ['quantity' => $product['quantity']];
        }

        $order->products()->attach($productData);

        $order->load('products');
        $order->total_price = $order->calculateTotalPrice();
        $order->quantity = $order->calculateQuantity();
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order created successfully',
        ], 201);
    }


    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->customer_id !== Auth::user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action',
            ], 403);
        }

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully',
        ], 200);
    }
}
