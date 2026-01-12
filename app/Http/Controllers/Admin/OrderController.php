<?php

// app/Http/Controllers/Admin/OrderController.php
namespace App\Http\Controllers\Admin;

use App\Events\OrderApproved;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status'); // 'pending', 'approved', 'rejected'
        $orders = Order::with('user');

        if ($status) {
            $orders->where('status', $status);
        }

        return view('admin.orders.index', [
            'orders' => $orders->latest()->paginate(10),
            'status' => $status
        ]);
    }
   
    public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected',
    ]);

    if ($request->status === 'approved') {
        // جلب عناصر الطلب مع المنتج والمتغير (variant)
        $order->load('items.product', 'items.variant');

        // تحقق من الكمية على مستوى الـ variant
        foreach ($order->items as $item) {
            $variant = $item->variant;

            if (!$variant) {
                return redirect()->back()->with('error', "المتغير للمنتج رقم {$item->product_id} غير موجود.");
            }

            if ($variant->stock_quantity < $item->quantity) {
                return redirect()->back()->with('error', "الكمية غير متوفرة للمنتج: {$item->product->name} باللون/المقاس المحدد.");
            }
        }

        // خصم الكميات من الـ variant والمنتج الأساسي
        foreach ($order->items as $item) {
            $variant = $item->variant;
            $variant->stock_quantity -= $item->quantity;
            $variant->save();

            $product = $item->product;
            $product->quantity -= $item->quantity;
            $product->save();
        }

        // حذف عناصر السلة الخاصة بالمستخدم (نفس المستخدم الذي قام بالطلب)
        // نفترض ان CartItem يحتوي على user_id و product_variant_id
        foreach ($order->items as $item) {
            \App\Models\CartItems::where('user_id', $order->user_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->delete();
        }

        // إنشاء سجل الدفع إذا لم يكن موجود
        $exists = Payment::where('order_id', $order->id)->exists();
        if (!$exists) {
            Payment::create([
                'order_id' => $order->id,
                'payment_code' => $order->payment_code,
            ]);
        }

        // إطلاق حدث عند الموافقة على الطلب
        event(new OrderApproved($order->id, $order->user_id));
    }

    // تحديث حالة الطلب
    $order->update(['status' => $request->status]);

    return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
}

    
 

public function show($id)
{
    $order = Order::with([
        'items.variant.product',
        'items.variant.color',
        'items.variant.size'
    ])->findOrFail($id);

    return view('admin.orders.show', compact('order'));
}


}
