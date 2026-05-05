<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
            return redirect()->route('admin.dashboard');
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
    // 1. Chuyển hướng người dùng sang Google
public function redirectToGoogle() {
    return Socialite::driver('google')->redirect();
}

// 2. Nhận dữ liệu từ Google trả về
public function handleGoogleCallback() {
    $googleUser = Socialite::driver('google')->user();

    // Tìm user có email này trong DB, nếu không có thì tạo mới
    $user = User::updateOrCreate([
        'google_id' => $googleUser->id,
    ], [
        'name' => $googleUser->name,
        'email' => $googleUser->email,
        'password' => bcrypt('123456dummy'), // Mật khẩu ảo
    ]);

    Auth::login($user);
    return redirect('/dashboard'); // Đăng nhập xong vào thẳng Dashboard
    }
}