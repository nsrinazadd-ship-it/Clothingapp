<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'gender',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public $timestamps = true;

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'category_subcategory',
            'subcategory_id',
            'category_id'
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
