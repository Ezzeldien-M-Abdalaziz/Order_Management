<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::factory(50)->create();
        $products = Product::all();

        foreach ($orders as $order) {
            $order->products()->attach(
                $products->random(3)->pluck('id')->mapWithKeys(function ($productId) {
                    return [
                        $productId => [
                            'quantity' => rand(1, 5),
                        ]
                    ];
                })->toArray()
            );

            $order->update([
                'total_price' => $order->calculateTotalPrice(),
                'quantity' => $order->calculateQuantity(),
            ]);
        }

    }
}
