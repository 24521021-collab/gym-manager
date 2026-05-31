<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\DefaultPasswordMail;

class ForgotPasswordController extends Controller
{
    public function sendResetEmail(Request $request)
    {
        // 1. Validate định dạng email
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Định dạng email không hợp lệ.'
        ]);

        // 2. Kiểm tra email có tồn tại trong bảng users không
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false, 
                'message' => 'Email này không tồn tại trong hệ thống.'
            ], 404);
        }

        // 3. Sinh mật khẩu mặc định 8 ký tự và cập nhật DB
        $newPassword = Str::random(8);
        $user->password = Hash::make($newPassword);
        /** @var \App\Models\User $user **/
        $user->save();

        // 4. Gửi Mail (Sử dụng Queue nếu có để tránh lag UI)
        try {
            Mail::to($user->email)->send(new DefaultPasswordMail($user->full_name, $newPassword));
            return response()->json([
                'success' => true, 
                'message' => 'Mật khẩu mới đã được gửi vào Email của bạn.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Lỗi gửi mail: ' . $e->getMessage()
            ], 500);
        }
    }
}
