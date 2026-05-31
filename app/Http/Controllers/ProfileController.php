<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        return view('profile');
    }

    public function changePassword(Request $request)
    {
        // 1. Validate chặt chẽ
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Bạn chưa nhập mật khẩu hiện tại',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải từ 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $user = Auth::user();

        // 2. So sánh mật khẩu cũ bằng Hash::check
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false, 
                'message' => 'Mật khẩu cũ không chính xác.'
            ], 422);
        }

        // 3. Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        /** @var \App\Models\User $user **/
        $user->save();

        return response()->json([
            'success' => true, 
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
}
