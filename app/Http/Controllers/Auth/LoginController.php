<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // hàm này giúp người dùng về trang login
    public function ShowLogin(){
        return view('login');
    }
    // hàm xử lý dữ liệu đăng nhập
    // dùng validate kiểm tra dữ liệu
    public function login(Request $request){
        $credentials=$request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);
    //ghi nhớ thông tin cho lần sau
    /* auth- authentification: kiểm tra dữ liệu trong dtbase*/
    if (Auth::attempt($credentials)){
        // C. Nếu đúng: Tạo lại Session (phiên làm việc) để bảo mật
        $request->session()->regenerate();
        $user= Auth::user();
        if($user->role =='admin'){
            return redirect()->route('admin_dashboard');
        }
        // D. Chuyển hướng người dùng về trang chủ
        return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
    }
    // Trả về nếu sai thông tin  
        return back()->withErrors(['email'=>'thông tin sai',])->onlyInput('email');
    }
    //logout
    public function logout(Request $request){
    Auth::logout(); // 1. Lệnh xóa trạng thái đăng nhập của người dùng
    $request->session()->invalidate(); // 2. Hủy bỏ phiên làm việc hiện tại
    $request->session()->regenerateToken(); // 3. Làm mới mã bảo mật (CSRF)
    return redirect()->route('trang_chu'); // 4. Đuổi về trang đăng nhập
    }
    
}