<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 

class RegisterController extends Controller
{
    //1 kiểm duyệt dữ liệu, hàm store lưu dữ liệu user
   public function store(Request $request)
    {
        // Xác định loại thông tin là email hay số điện thoại
        $isEmail = filter_var($request->login, FILTER_VALIDATE_EMAIL);
        $loginField = $isEmail ? 'email' : 'phone';

        $request->validate([
            'full_name' => 'required|string|max:255',
            'login' => [
                'required',
                'string',
                $isEmail ? 'email' : 'numeric', // Nếu không phải email thì bắt buộc là số
                "unique:user,$loginField"
            ],
            'password' => 'required|string|min:8',
        ],
        [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'login.required' => 'Vui lòng nhập Email hoặc Số điện thoại.',
            'login.email' => 'Email không đúng định dạng.',
            'login.numeric' => 'Số điện thoại phải là chữ số.',
            'login.unique' => 'Thông tin này đã được sử dụng bởi thành viên khác.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        // 2. Lưu vào database
        $user = User::create([
            'full_name' =>$request->full_name,
            $loginField => $request->login,
            'password' => Hash::make($request->password),
            'role' => 'guest', // Mặc định là khách vãng lai sau khi đăng ký
        ]);

        // Tự động đăng nhập người dùng ngay sau khi đăng ký thành công, tạo sêssion mới để bảo mật
        Auth::login($user);
        $request->session()->regenerate();

        // 3. Chuyển hướng về trang chủ kèm thông báo
        return redirect('/')->with('success', 'Đăng ký thành công!');
    }
}