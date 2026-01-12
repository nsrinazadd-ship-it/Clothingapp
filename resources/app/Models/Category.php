<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

     protected $fillable = [
            'name',
        ];
        protected $hidden=['created_at',
                'updated_at'];
       Public $timestamps=true;



    public function subcategories()
{
    return $this->belongsToMany(
        Subcategory::class,
        'category_subcategory',
        'category_id',
        'subcategory_id'
    );
}

public function products()
{
    return $this->hasMany(Product::class);
}


}
