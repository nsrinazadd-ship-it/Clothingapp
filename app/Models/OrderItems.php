<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;
     protected $fillable = [
            'order_id', 
            'product_id',
            'price',
            'quantity',
            'product_variant_id', 


        ];
        protected $hidden=['created_at',
                'updated_at'];

        public function order() {
            return $this->belongsTo(Order::class);
        }
    
        public function product() {
            return $this->belongsTo(Product::class);
        }
        public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
