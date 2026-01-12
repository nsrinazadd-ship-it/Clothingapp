@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">عناصر الطلب رقم #{{ $order->id }}</h1>

    <a href="{{ route('admin.orders.index', $order->id) }}" class="btn btn-sm btn-primary">← الرجوع إلى الطلبات</a>

    <table class="w-full bg-white shadow rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">اسم المنتج</th>
                <th class="p-2">القياس</th>
                <th class="p-2">اللون</th>
                <th class="p-2">السعر</th>
                <th class="p-2">الكمية</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $item)
                <tr class="border-b">
                    <td class="p-2">{{ $item->variant->product->name ?? 'غير متوفر' }}</td>
                    <td class="p-2">{{ $item->variant->size->name ?? 'غير متوفر' }}</td>
                    <td class="p-2">{{ $item->variant->color->name ?? 'غير متوفر' }}</td>
                    <td class="p-2">{{ $item->price }} ل.س</td>
                    <td class="p-2">{{ $item->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">لا توجد عناصر في هذا الطلب.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
