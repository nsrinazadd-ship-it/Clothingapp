<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItems;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function checkout(Request $request)
{
    $request->validate([
        'shipping_address' => 'required|string',
        'payment_method' => 'required|in:syriatel_cash,bank_notice',
        'payment_code' => 'nullable|string',
        'receipt_image' => 'nullable|image|max:2048'
    ]);

    $user = Auth::user();

    $cartItems = CartItems::with(['product', 'variant'])->where('user_id', $user->id)->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'عربة التسوق فارغة'], 400);
    }

    // جلب الطلبات المفتوحة للمستخدم (شاملة pending, approved, rejected)
    $existingOrders = Order::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'approved', 'rejected'])
        ->with('items')
        ->get();

    foreach ($existingOrders as $order) {
        if (
            strtolower(trim($order->shipping_address)) === strtolower(trim($request->shipping_address)) &&
            strtolower(trim($order->payment_method)) === strtolower(trim($request->payment_method)) &&
            strtolower(trim($order->payment_code ?? '')) === strtolower(trim($request->payment_code ?? ''))
        ) {
            $orderItems = $order->items;

            if ($orderItems->count() === $cartItems->count()) {
                $allMatch = true;
                foreach ($cartItems as $cartItem) {
                    $match = $orderItems->firstWhere(function ($orderItem) use ($cartItem) {
                        return
                            $orderItem->product_id == $cartItem->product_id &&
                            $orderItem->product_variant_id == $cartItem->product_variant_id &&
                            $orderItem->quantity == $cartItem->quantity;
                    });

                    if (!$match) {
                        $allMatch = false;
                        break;
                    }
                }

                if ($allMatch) {
                    return response()->json([
                        'message' => 'لقد قمت بإرسال طلب مطابق سابقاً. لا يمكنك إرسال نفس الطلب مرتين.'
                    ], 400);
                }
            }
        }
    }

    DB::beginTransaction();

    try {
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item->product && $item->variant) {
                $totalPrice += $item->product->price * $item->quantity;
            } else {
                return response()->json(['message' => 'بعض العناصر في العربة غير صالحة'], 400);
            }
        }

        $order = Order::create([
            'user_id' => $user->id,
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'payment_code'=> $request->payment_code,
            'total_price' => $totalPrice,
        ]);

        foreach ($cartItems as $item) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        // حذف عناصر العربة بعد إنشاء الطلب
        CartItems::where('user_id', $user->id)->delete();

        DB::commit();

        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح، في انتظار موافقة الإدارة',
            'order' => $order
        ], 201);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'error' => 'حدث خطأ أثناء معالجة الطلب',
            'details' => $e->getMessage()
        ], 500);
    }
}






    
public function getOrdersByStatus($status)
{
    $user = Auth::user();

    if (!in_array($status, ['pending', 'approved', 'rejected'])) {
        return response()->json(['error' => 'Invalid status'], 400);
    }

    $orders = Order::with([
        'items.variant' => function ($query) {
            $query->with([
                'product:id,name,image', // اسم المنتج وصورته
                'color:id,name',
                'size:id,name'
            ]);
        }
    ])
    ->where('user_id', $user->id)
    ->where('status', $status)
    ->orderBy('created_at', 'desc')
    ->get(['id', 'user_id', 'status', 'created_at']);

    return response()->json([
        'orders' => $orders
    ]);
}

}