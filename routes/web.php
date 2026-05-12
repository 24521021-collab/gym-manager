<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Admin\GymPackageController;
use App\Http\Controllers\UserMembershipController;
use App\Http\Controllers\Admin\AdminMembershipController;
use App\Http\Controllers\BodyMetricController;
use App\Http\Controllers\Admin\CheckInController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BookingController;

// 1. Trang chủ (hiện chữ chào mừng hoặc file welcome có sẵn)

//home( trang chủ )
Route::get('/',[HomeController::class, 'index'])->name('trang_chu');
//gói tập
Route::get('/goi-tap', [HomeController::class, 'index']);
// nhận giữ liệu đăng nhập từ form user gửi tới

Route::post('/dang_ky',[RegisterController::class,'store'])->name('register');
// nhan thong tin dang ky va luu
Route::get('/dang_nhap',[LoginController::class,'ShowLogin'])->name('login');
Route::post('/dang_nhap',[LoginController::class,'login'])->name('login.post');
//dang xuat tai khoan
Route::post('/logout',[LoginController::class,'logout'])->name('logout');

Route::get('/my_membership',[UserMembershipController::class,'MyMembership'])->name('my.membership');
// Gom nhóm tất cả route bắt đầu bằng /admin
Route::middleware(['auth', 'CheckRole:admin'])->prefix('admin')->group(function () {
    // Trang chủ Admin
    Route::get('/dashboard', function () {
        return view('admin_dashboard');
    })->name('admin.dashboard');

    // CRUD Hội viên 
    // 1. Route chính cho admin quản lý hội viên (Index, Store, Update, Destroy)
    // Tên route sẽ tự động là: memberships.index, memberships.store, v.v...
    Route::resource('members', AdminMembershipController::class);
    
    // Các route quản lý gói tập khác của bạn...
    // route quản lý gói tặp CRUD của admin
    Route::resource('packages',GymPackageController::class);
});
// Route lưu gói tập khách hàng 
Route::post('/register-package', [UserMembershipController::class, 'register'])->name('membership.register');
//route CRUD thông tin gói tập khách hàng cho admin
// Nhóm các Route dành cho Admin (Nên có middleware 'auth' để bảo mật)

//route để xem body metric, chỉ người đăng nhập mới xem được vì có middleware,dùng put để update body metric theo từng giai đoạn 
Route::middleware('auth')->group(function () {
    Route::get('/body_metric', [BodyMetricController::class, 'index'])->name('body.metric');
    Route::put('/body_metric/update', [BodyMetricController::class, 'update'])->name('metric.update');
});
// route để người dùng thực hiện checkins
Route::get('/admin/checkin', [CheckInController::class, 'index'])->name('admin.checkin');
Route::post('/admin/checkin/store', [CheckInController::class,'store'])->name('admin.checkin.store');
    /// test 
    Route::get('/test-data', function() {return \App\Models\GymPackage::all();});
//// route để đăng nhập vào google
// 1. Đường dẫn để nhấn nút "Đăng nhập bằng Google"
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// 2. Đường dẫn để Google trả dữ liệu về (phải khớp 100% với link đã khai báo trên Google Console)
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
// route đăng nhập bằng fb
Route::get('auth/facebook', [LoginController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [LoginController::class, 'handleFacebookCallback']);


use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

// Trang danh sách sản phẩm (Cho phép mọi người xem nhưng không mua được nếu chưa login)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Nhóm các đường dẫn chỉ dành cho người đã đăng nhập
// API giỏ hàng 
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add/{id}', [CartController::class, 'add']);
Route::patch('cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('cart/checkout', [CartController::class, 'checkout'])->middleware('auth')->name('cart.checkout');
// Route lấy API giỏ hàng để tìm kiếm sản phẩm
Route::get('/search-products', [ProductController::class, 'getProductsApi']);
// trang checkout, xác nhận hình thức thanh toán 
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
Route::get('/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
// ─── Lớp học (Bookings) ──────────────────────────────────────────────────────
Route::get('/classes',           [BookingController::class, 'index'])->name('classes.index');
Route::post('/classes/book',     [BookingController::class, 'store'])->name('classes.store')->middleware('auth');
Route::delete('/classes/cancel', [BookingController::class, 'cancel'])->name('classes.cancel')->middleware('auth');