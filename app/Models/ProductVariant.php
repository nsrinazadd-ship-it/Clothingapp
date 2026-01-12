<?php
namespace App\Models;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'color_id', 'size_id', 'stock_quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(ProductSize ::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItems::class, 'product_variant_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'product_variant_id');
    }
}
