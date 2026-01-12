@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">لوحة تحكم الأدمن</h1>

    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary w-100 mb-3">الطلبات</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100 mb-3">المستخدمين</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.products.index') }}" class="btn btn-success w-100 mb-3">المنتجات</a>
        </div>
    </div>
</div>
@endsection