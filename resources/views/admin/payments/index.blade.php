@extends('layouts.app')

@section('title', 'قائمة الدفعات')

@section('content')
<div class="container">
    <h2 class="mb-4">قائمة الدفعات</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>كود الدفع</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->order_id }}</td>
                    <td>{{ $payment->payment_code ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">لا توجد بيانات.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $payments->links() }}
    </div>
</div>
@endsection