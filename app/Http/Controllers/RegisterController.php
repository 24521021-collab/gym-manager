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
        ]);

        // 2. Lưu vào database
        user::create([
            'full_name' =>$request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member',
        ]);

        // 3. Chuyển hướng về trang chủ kèm thông báo
        return redirect('/')->with('success', 'Đăng ký thành công!');
    }
}