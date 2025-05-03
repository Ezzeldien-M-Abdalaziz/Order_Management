<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    public function getProducts()
    {
        $products = Product::paginate(10);
            $data = $products->toArray();
            $data['data'] = $products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'description' => $p->description,
                'stock' => $p->stock,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => $data,
            ], 200);
        }

        public function getProduct($productId)
        {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product,
            ], 200);
        }



}
