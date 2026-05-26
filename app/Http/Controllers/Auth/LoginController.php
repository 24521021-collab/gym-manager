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
        if($user->role =='pt'){
            return redirect()->route('pt.dashboard');
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

    // Tìm user theo email để tránh trùng lặp nếu họ đã đăng ký bằng form trước đó
    $user = User::where('email', $googleUser->email)->first();

    if (!$user) {
        $user = User::create([
            'google_id' => $googleUser->id,
            'full_name' => $googleUser->name,
            'role'      => 'guest', // Tài khoản mới từ Google mặc định là guest
            'email'     => $googleUser->email,
            'password'  => bcrypt('12345678'),
        ]);
    } elseif (!$user->google_id) {
        $user->update(['google_id' => $googleUser->id]);
    }

    Auth::login($user);
    return redirect('/'); // Đăng nhập xong vào thẳng Dashboard
    }

}