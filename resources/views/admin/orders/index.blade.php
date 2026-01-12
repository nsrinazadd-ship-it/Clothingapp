<!-- resources/views/admin/orders/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">قائمة الطلبات</h1>

        <!-- الفلترة -->
        <form method="GET" class="mb-4">
            <select name="status" onchange="this.form.submit()" class="p-2 border rounded">
                <option value="">كل الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
            </select>
        </form>

        <!-- جدول الطلبات -->
        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">المستخدم</th>
                    <th class="p-2">العنوان</th>
                    <th class="p-2">طريقة الدفع</th>
                    <th class="p-2">الحالة</th>
                    <th class="p-2">كود الدفع</th>
                    <th class="p-2">تغيير الحالة</th>
                    <th class="p-2">العناصر</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="border-b">
                        <td class="p-2">
                            @if($order->user)
                                {{ $order->user->name }}
                            @else
                                <span class="text-red-500">تم حذف المستخدم</span>
                            @endif
                        </td>

                        <td class="p-2">{{ $order->shipping_address }}</td>
                        <td class="p-2">{{ $order->payment_method }}</td>
                        <td class="p-2">{{ $order->status }}</td>
                        <td class="p-2">{{ $order->payment_code }}</td>
                        <td class="p-2">
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="border p-1 rounded">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>قيد الانتظار
                                    </option>
                                    <option value="approved" {{ $order->status == 'approved' ? 'selected' : '' }}>موافق عليه
                                    </option>
                                    <option value="rejected" {{ $order->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">عرض
                                العناصر</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- روابط الصفحات -->
        <div class="mt-4">
            {{ $orders->appends(['status' => request('status')])->links() }}
        </div>
    </div>
@endsection