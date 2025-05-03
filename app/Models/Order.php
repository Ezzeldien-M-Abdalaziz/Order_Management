<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'total_price',
        'quantity',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }


        public function calculateTotalPrice(): float
        {
            $total = 0;
            foreach ($this->products as $product) {
                $total += $product->price * $product->pivot->quantity;
            }
            return $total;
        }

        public function calculateQuantity(): float
        {
            $total = 0;
            foreach ($this->products as $product) {
                $total += $product->pivot->quantity;
            }
            return $total;
        }


}
