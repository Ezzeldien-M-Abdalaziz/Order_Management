<?php

namespace App\Http\Controllers\Api\Admin;

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


    public function createProduct(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
        ],
    [
        'name.required' => 'Product name is required',
        'price.required' => 'Product price is required',
        'stock.required' => 'Product stock is required',
        'description.string' => 'Product description must be a string',
        'stock.integer' => 'Product stock must be an integer',
        'price.numeric' => 'Product price must be a number',
        ]);

        Product::create($fields);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
        ], 201);

    }

    public function updateProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
        ],[
            'id.required' => 'Product ID is required',
            'id.exists' => 'Product not found',
            'name.required' => 'Product name is required',
            'price.required' => 'Product price is required',
            'stock.required' => 'Product stock is required',
            'description.string' => 'Product description must be a string',
            'stock.integer' => 'Product stock must be an integer',
            'price.numeric' => 'Product price must be a number',
        ]);

        $product = Product::find($request->id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }


        $product->update($request->only(['name', 'price', 'description', 'stock']));

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
        ], 200);
    }

    public function deleteProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
