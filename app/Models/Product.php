<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;


class Product extends Model
{
    use HasFactory;
    // في Product.php
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'quantity',
        'category_id',
        'subcategory_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public $timestamps = true;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }


    public function cartItems()
    {
        return $this->hasMany(CartItems::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }


}
