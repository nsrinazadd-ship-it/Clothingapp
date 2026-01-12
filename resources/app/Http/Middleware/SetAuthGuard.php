<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SetAuthGuard
{
    public function handle($request, Closure $next, $guard = 'web')
    {
        // تحديد الـ guard
        Auth::shouldUse($guard);

        // محاولة تسجيل الدخول باستخدام هذا الـ guard
        if (Auth::guard($guard)->check()) {
            return $next($request);
        }

        // إذا لم يكن التوكن صالحًا لهذا الـ guard
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
