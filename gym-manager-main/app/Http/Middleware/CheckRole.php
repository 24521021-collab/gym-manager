<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Vui lòng đăng nhập để tiếp tục.'], 401);
            }
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập trước.');
        }

        $user = Auth::user();
        
        // 2. Kiểm tra vai trò (Hỗ trợ nhiều vai trò cùng lúc)
        // in_array giúp kiểm tra xem role của user có nằm trong danh sách được phép không
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Trả về lỗi 403 phù hợp với loại request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'Bạn không có quyền truy cập chức năng này.'], 403);
        }

        abort(403, 'Bạn không có quyền truy cập chức năng này.');
    }
}