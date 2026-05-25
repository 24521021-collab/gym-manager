<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController; // Import Controller mới
use App\Http\Controllers\ProfileController; // Import ProfileController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Admin\GymPackageController;
use App\Http\Controllers\UserMembershipController;
use App\Http\Controllers\Admin\AdminMembershipController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\BodyMetricController;
use App\Http\Controllers\Admin\CheckInController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\ClassController; // Đảm bảo import đúng
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\PtBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PT\PtClassController;
use App\Http\Controllers\Pt\PtDashboardController;
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
    // Trang chủ Admin gồm biểu đồ doanh thu
    Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
    // API phục vụ cho hàm fetchProductData() trong file Blade
    Route::get('/dashboard/filter-products', [DashboardController::class, 'filterProducts']);
    // CRUD Hội viên 
    // 1. Route chính cho admin quản lý hội viên (Index, Store, Update, Destroy)
    // Tên route sẽ tự động là: memberships.index, memberships.store, v.v...
    Route::resource('members', AdminMembershipController::class);
    
    // Các route quản lý gói tập khác của bạn...
    // route quản lý gói tặp CRUD của admin
    Route::resource('packages',GymPackageController::class);
    //trang admin quản lý các lớp học 
    Route::resource('gym-classes', ClassController::class)->names('admin.gym-classes');
    //trang admin quản lý sản phẩm
    Route::resource('products',AdminProductController::class)->names('admin.products');
    // trang admin quản lý các đơn hàng
    // trang admin quản lý các trạng thái đơn hàng
    Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::put('/orders/update-status/{id}',[AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
});

// Nhóm Route dành riêng cho PT
Route::middleware(['auth', 'CheckRole:pt'])->prefix('pt')->group(function () {
    Route::get('/dashboard', [PtDashboardController::class, 'index'])->name('pt.dashboard');
    
    // Quản lý lớp học (Sử dụng Resource để rút gọn)
    Route::resource('classes', PtClassController::class)->names('pt.classes')->only(['index', 'store', 'update', 'destroy']);

    // Quản lý đặt lịch riêng
    Route::get('/bookings', [PtDashboardController::class, 'bookings'])->name('pt.bookings.index');
    Route::patch('/bookings/{id}/status', [PtDashboardController::class, 'updateBookingStatus'])->name('pt.bookings.updateStatus');
});

// Route lưu gói tập khách hàng 
Route::post('/register-package', [UserMembershipController::class, 'register'])->name('membership.register');
//route CRUD thông tin gói tập khách hàng cho admin
// Nhóm các Route dành cho Admin (Nên có middleware 'auth' để bảo mật)

//route để xem body metric, chỉ người đăng nhập mới xem được vì có middleware,dùng put để update body metric theo từng giai đoạn 
Route::middleware('auth')->group(function () {
    Route::get('/body_metric', [BodyMetricController::class, 'index'])->name('body.metric');
    Route::put('/body_metric/update', [BodyMetricController::class, 'update'])->name('metric.update');
    // route để xem đơn hàng người dùng 
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');

    // ─── Thanh toán (Checkout) ──────────────────────────────────────────
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/checkout/bank-qr/{order_id}', [CheckoutController::class, 'showBankQR'])->name('checkout.bank_qr');
    Route::get('/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');

 // ─── ĐẶT LỊCH RIÊNG PT (THÊM VÀO ĐÂY) ───────
    Route::get('/booking-pt', [PtBookingController::class, 'index'])->name('booking.pt.index');
    Route::get('/api/pt-booked-slots', [PtBookingController::class, 'getBookedSlots'])->name('api.pt.booked_slots');
    Route::post('/booking-pt', [PtBookingController::class, 'store'])->name('booking.pt.store');

    // Quên & Đổi mật khẩu
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('password.change');
});

// Route quên mật khẩu công khai
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetEmail'])->name('password.forgot.post');


// route để người dùng thực hiện checkins
Route::get('/admin/checkin', [CheckInController::class, 'index'])->name('admin.checkin');
Route::post('/admin/checkin/store', [CheckInController::class,'store'])->name('admin.checkin.store');


    /// test 
    Route::get('/test-data', function() {return \App\Models\GymPackage::all();});

// route để đăng nhập vào google
// 1. Đường dẫn để nhấn nút "Đăng nhập bằng Google"
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// 2. Đường dẫn để Google trả dữ liệu về (phải khớp 100% với link đã khai báo trên Google Console)
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
// route đăng nhập bằng fb

// Route này nhận ID của lớp học để biết đang đăng ký lớp nào
Route::post('/booking/{id}', [BookingController::class, 'store'])->name('booking.store')->middleware('auth');
//

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

// ─── Lớp học (Bookings) ──────────────────────────────────────────────────────
Route::get('/classes',[BookingController::class, 'index'])->name('classes.index');
Route::post('/classes/book',[BookingController::class, 'store'])->name('classes.store')->middleware('auth');
Route::delete('/classes/cancel',[BookingController::class, 'cancel'])->name('classes.cancel')->middleware('auth');
