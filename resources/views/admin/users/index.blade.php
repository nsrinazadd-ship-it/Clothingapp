@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <h2>المستخدمون</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>الدور</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role == 'admin')
                        <span class="badge bg-danger">أدمن</span>
                    @else
                        <span class="badge bg-secondary">مستخدم</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection