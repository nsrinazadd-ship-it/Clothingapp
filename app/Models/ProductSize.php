<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{    use HasFactory;

    protected $table = 'sizes';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
