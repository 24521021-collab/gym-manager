<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 

class RegisterController extends Controller
{
    //1 kiểm duyệt dữ liệu, hàm store lưu dữ liệu user
   public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'password' => 'required|string|min:8',
        ],
        [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        // 2. Lưu vào database
        user::create([
            'full_name' =>$request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guest', // Mặc định là khách vãng lai sau khi đăng ký
        ]);

        // 3. Chuyển hướng về trang chủ kèm thông báo
        return redirect('/')->with('success', 'Đăng ký thành công!');
    }
}