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
     */
    public function handle(Request $request, Closure $next): Response
    {
         {
        if (!Auth::check()) {
            return redirect()->route('trang_chu');
        }

        $userRole = Auth::user()->role;

        if (empty($roles)) {
            $roles = ['admin','user','trainer'];
        }

        // Kiểm tra xem role của user
        if (in_array($userRole, $roles)) {
            return $next($request);
        }
        
        abort(403, 'Bạn không có quyền truy cập chức năng này.');
    }
    }
}
