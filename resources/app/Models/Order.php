<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'shipping_address',
        'payment_method',
        'status',
        'payment_code',
        'total_price',
    ];
    protected $hidden=['created_at',
                'updated_at'];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItems::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    } 
    

}

