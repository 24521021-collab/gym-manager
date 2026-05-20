<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập trước.');
        }

        // 2. Kiểm tra vai trò của người dùng có khớp với vai trò yêu cầu từ Route không
        if (Auth::user()->role === $role) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập chức năng này.');
    }
}