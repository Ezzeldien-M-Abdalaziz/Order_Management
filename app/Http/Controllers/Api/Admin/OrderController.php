<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController
{
    public function getOrders()
    {
        $orders = Order::with('customer' , 'products')->paginate(10);
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
            ]),
            'customer_id' => $order->customer_id,
            'customer_name' => $order->customer->name,
            'customer_email' => $order->customer->email,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function getOrder($orderId)
    {
        $order = Order::with('customer', 'products')->find($orderId);

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

    public function updateOrder(Request $request, $orderId)
    {

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

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|string|in:pending,shipped,cancelled',
        ], [
            'order_id.required' => 'Order ID is required',
            'order_id.exists' => 'Order not found',
            'status.required' => 'Status is required',
            'status.string' => 'Status must be a string',
            'status.in' => 'Status must be one of the following: pending, shipped, cancelled',
        ]);

        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => 'Order status updated successfully',
        ], 200);
    }

    public function getStats()
{
    $totalRevenue = Order::where('status', 'shipped')->sum('total_price');

    $statusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get()
        ->pluck('count', 'status');

    return response()->json([
        'total_revenue' => $totalRevenue,
        'orders_per_status' => $statusCounts
    ]);
}


    public function shippedOrders()
    {
        $shippedOrders = Order::where('status', 'shipped')->with('customer', 'products')->paginate(10);
        if ($shippedOrders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No shipped orders found',
            ], 404);
        }
        $data = $shippedOrders->toArray();

        $data['data'] = $shippedOrders->map(fn($order) => [
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
            ]),
            'customer_id' => $order->customer_id,
            'customer_name' => $order->customer->name,
            'customer_email' => $order->customer->email,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Shipped orders retrieved successfully',
            'data' => $data,
        ], 200);
    }
}
