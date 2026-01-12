<?php

namespace App\Http\Controllers;

use App\Models\CartItems;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVariant;


class CartController extends Controller
{
    // 1. عرض محتويات السلة (صفحة My Cart)
    public function index()
{
    $cartItems = Auth::user()
        ->cartItems()
        ->with(['product', 'variant.color', 'variant.size']) // لازم عامل علاقات color و size بـ ProductVariant
        ->get();

    $cartData = $cartItems->map(function ($item) {
        return [
            'cart_item_id' => $item->id,
            'quantity'     => $item->quantity,
            'product'      => [
                'id'    => $item->product->id,
                'name'  => $item->product->name,
                'price' => $item->product->price,
                'image' => $item->product->image,
            ],
            'variant' => [
                'id'    => $item->variant->id,
                'color' => $item->variant->color->name ?? null,
                'size'  => $item->variant->size->name ?? null,
            ]
        ];
    });

    return response()->json([
        'status' => 'success',
        'data'   => $cartData,
    ]);
}

    // 2. إضافة منتج للسلة (Add to Cart)
    public function store(Request $request, $productId)
{
    // تحقق من صحة البيانات الواردة
    $request->validate([
        'product_variant_id' => 'required|integer|exists:product_variants,id',
    ]);

    $user = Auth::user();

    // جلب المنتج الأساسي
    $product = Product::findOrFail($productId);

    // جلب الـ variant والتأكد انه تابع لهذا المنتج
    $variant = ProductVariant::where('id', $request->product_variant_id)
        ->where('product_id', $product->id)
        ->first();

    if (!$variant) {
        return response()->json([
            'message' => 'المقاس أو اللون المحدد غير تابع لهذا المنتج.',
        ], 400);
    }

    // التحقق من توفر الكمية في الـ variant
    if ($variant->stock_quantity < 1) {
        return response()->json([
            'message' => 'المنتج غير متوفر حالياً بهذا اللون والمقاس.',
        ], 400);
    }

    // تحقق هل العنصر موجود مسبقًا لنفس المستخدم والـ variant
    $existing = CartItems::where('user_id', $user->id)
        ->where('product_id', $product->id)
        ->where('product_variant_id', $variant->id)
        ->first();

    if ($existing) {
        // تحقق اذا اضافة كمية جديدة تتجاوز المخزون
        if ($existing->quantity + 1 > $variant->stock_quantity) {
            return response()->json([
                'message' => 'الكمية المطلوبة غير متوفرة في المخزون لهذا الخيار.',
            ], 400);
        }

        // زيادة الكمية
        $existing->quantity += 1;
        $existing->save();
    } else {
        // إنشاء عنصر سلة جديد
        $existing = CartItems::create([
            'user_id'            => $user->id,
            'product_id'         => $product->id,
            'product_variant_id' => $variant->id,
            'quantity'           => 1,
        ]);
    }

    return response()->json([
        'message'    => 'تم إضافة المنتج إلى السلة.',
        'cart_item'  => $existing
    ]);
}


    // 3. تحديث كمية عنصر في السلة (اختياري)
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItems::findOrFail($cartItemId);

        // تأكد إن العنصر ينتمي فعليًا للمستخدم الحالي
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'تم تعديل الكمية بنجاح.',
            'cart_item' => $cartItem
        ]);
    }
 
    ///////////////////////////////
    public function increment($cartItemId)
{
    $cartItem = CartItems::with('variant')->findOrFail($cartItemId);

    if ($cartItem->user_id !== Auth::id()) return response()->json(['message' => 'غير مصرح'], 403);

    if ($cartItem->quantity + 1 > $cartItem->variant->stock_quantity) {
        return response()->json(['message' => 'الكمية غير كافية.'], 400);
    }

    $cartItem->quantity += 1;
    $cartItem->save();

    return response()->json(['message' => 'تمت الزيادة', 'cart_item' => $cartItem]);
}

public function decrement($cartItemId)
{
    $cartItem = CartItems::findOrFail($cartItemId);

    if ($cartItem->user_id !== Auth::id()) return response()->json(['message' => 'غير مصرح'], 403);

    if ($cartItem->quantity > 1) {
        $cartItem->quantity -= 1;
        $cartItem->save();

        return response()->json(['message' => 'تم النقصان', 'cart_item' => $cartItem]);
    }

    return response()->json(['message' => 'لا يمكن تقليل الكمية لأقل من 1.'], 400);
}




    // 4. إزالة عنصر من السلة (Remove from Cart)
    public function destroy($cartItemId)
    {
        $cartItem = CartItems::findOrFail($cartItemId);

        // تأكد إن العنصر ينتمي فعليًا للمستخدم
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'تم حذف المنتج من السلة.'
        ]);
    }
}