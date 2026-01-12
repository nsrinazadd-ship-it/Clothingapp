<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // عرض المنتجات المفضلة
    public function index()
    {
        $user = Auth::user();
    
        $favorites = $user->favorites()
            ->select('products.id', 'products.name', 'products.price', 'products.image')
            ->get()
            ->makeHidden('pivot')  // يخفي حقل pivot من النتائج
            ->map(function ($product) {
                $product->is_favorite = true;
                return $product;
            });
    
        return response()->json($favorites);
    }

public function store($productId)
{
    $user = Auth::user();
    if (!$user->favorites->contains($productId)) {
        $user->favorites()->attach($productId); //  استخدم () هنا
    }

    return response()->json(['message' => 'Added to favorites']);
}

public function destroy($productId)
{
    $user = Auth::user();
    $user->favorites()->detach($productId); //  استخدم () هنا

    return response()->json(['message' => 'Removed from favorites']);
}
    // إضافة منتج للمفضلة


}

