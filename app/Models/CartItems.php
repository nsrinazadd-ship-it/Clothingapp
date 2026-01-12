<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    use HasFactory;
    protected $fillable = [
            'user_id',
            'product_id',
            'quantity',
            'product_variant_id'
    

        ];
        protected $hidden=['created_at',
                'updated_at'];


        public function user()
    {
        return $this->belongsTo(User::class);
    }

    // كل CartItem ينتمي لمنتج واحد
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    
}
