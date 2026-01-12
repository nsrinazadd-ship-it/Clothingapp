<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'لوحة التحكم')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: #343a40; color: white; min-height: 100vh; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; }
        .sidebar a:hover { background: #495057; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <a href="{{ route('admin.dashboard') }}" style="color: white; text-decoration: none;">لوحة التحكم</a>
            <a href="{{ route('admin.products.index') }}">المنتجات</a>
            <a href="{{ route('admin.users.index') }}">المستخدمين</a>
            <a href="{{ route('admin.payments.index') }}">الدفعات</a>
            <a href="{{ route('admin.orders.index') }}">الطلبات</a>
            <a href="{{ route('logout') }}">تسجيل الخروج</a>
        </div>

        <!-- Content -->
        <div class="col-md-10 p-4">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>