<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // شرط أن المستخدم مسجل دخول وصلاحيته admin
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // إذا مش ادمن، ارجع لصفحة رئيسية أو 403
        abort(403, 'ليس لديك صلاحية الدخول لهذه الصفحة.');
    }
}
