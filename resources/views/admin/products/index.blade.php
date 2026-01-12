@extends('layouts.app')

@section('title', 'إدارة التركيبات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>التركيبات (الألوان والمقاسات لكل منتج)</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">إضافة منتج جديد</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>الصورة</th>
                <th>اسم المنتج</th>
                <th>اللون</th>
                <th>المقاس</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>القسم</th>
                <th>الإجراء</th>
            </tr>
        </thead>
        <tbody>
        @foreach($variants as $variant)
            <tr>
                <td><img src="{{ asset('storage/' . $variant->product->image) }}" width="70"></td>
                <td>{{ $variant->product->name }}</td>
                <td>{{ $variant->color->name }}</td>
                <td>{{ $variant->size->name }}</td>
                <td>{{ $variant->stock_quantity }}</td>
                <td>{{ number_format($variant->product->price, 2) }} ل.س</td>
                <td>{{ $variant->product->category->name ?? '-' }}</td>
                <td>
                    <a href="{{ route('admin.products.edit', $variant->product->id) }}" class="btn btn-sm btn-primary">تعديل المنتج</a>
                    <form action="{{ route('admin.products.destroy', $variant->product->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('هل أنت متأكد؟')" class="btn btn-sm btn-danger">حذف المنتج</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $variants->links() }}
@endsection
