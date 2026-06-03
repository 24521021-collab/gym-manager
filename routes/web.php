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
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminPt_BookingController;
use App\Http\Controllers\Admin\PostController;
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
// //
// Gom nhóm tất cả route bắt đầu bằng /admin
Route::middleware(['auth', 'CheckRole:admin'])->prefix('admin')->group(function () {
    // Trang chủ Admin gồm biểu đồ doanh thu
    Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
    // API phục vụ cho hàm fetchProductData() trong file Blade
    Route::get('/dashboard/filter-products', [DashboardController::class, 'filterProducts']);
    // CRUD Hội viên 
    // 1. Route chính cho admin quản lý hội viên 
    Route::get('memberships', [AdminMembershipController::class,'index'])->name('memberships.index');
    Route::post('members/{id}/send-expiration-notification', [AdminMembershipController::class, 'sendExpirationNotification'])->name('admin.members.sendExpirationNotification');
    
    // Các route quản lý gói tập khác của bạn...
    // route quản lý gói tặp CRUD của admin
    Route::resource('packages',GymPackageController::class);
    //trang admin quản lý các lớp học 
    Route::resource('gym-classes', ClassController::class)->names('admin.gym-classes');
    //trang admin quản lý sản phẩm
    Route::resource('products',AdminProductController::class)->names('admin.products');
    // trang admin quản lý các đơn hàng
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::get('transaction', [AdminOrderController::class, 'index'])->name('admin.transaction');
    Route::put('/orders/update-status/{id}',[AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    // Quản lý lịch đặt PT
    Route::get('pt-bookings', [AdminPt_BookingController::class, 'index'])->name('admin.pt-bookings');
    // Quản lý bài viết blog 
    Route::resource('posts', PostController::class)->names('admin.posts');
    // route để thực hiện checkins
    Route::post('/checkin/store', [CheckInController::class,'store'])->name('admin.checkin.store');
});

// Nhóm Route dành riêng cho PT
Route::middleware(['auth', 'CheckRole:pt'])->prefix('pt')->group(function () {
    Route::get('/dashboard', [PtDashboardController::class, 'index'])->name('pt.dashboard');
    
    // Quản lý lớp học (Sử dụng Resource để rút gọn)
    Route::resource('classes', PtClassController::class)->names('pt.classes')->only(['index', 'store', 'update', 'destroy']);

    // Quản lý đặt lịch riêng
    Route::get('/bookings', [PtDashboardController::class, 'bookings'])->name('pt.bookings.index');
    Route::patch('/bookings/{id}/status', [PtDashboardController::class, 'updateBookingStatus'])->name('pt.bookings.updateStatus');

    // Lưu nhật ký huấn luyện
    Route::post('/logs/store', [PtDashboardController::class, 'storeLog'])->name('pt.logs.store');
});


//route để xem body metric, chỉ người đăng nhập mới xem được vì có middleware,dùng put để update body metric theo từng giai đoạn 
Route::middleware('auth')->group(function () {
    // profile người dùng 
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    // route để xem đơn hàng người dùng 
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');
// route để người dùng đánh giá
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/api/reviewable-targets', [ReviewController::class, 'getReviewableTargets'])->name('reviews.targets');

    // ─── Thanh toán (Checkout) ──────────────────────────────────────────
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/checkout/bank-qr/{order_id}', [CheckoutController::class, 'showBankQR'])->name('checkout.bank_qr');

    // Quên & Đổi mật khẩu
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('password.change');

    // Route lưu gói tập khách hàng (Yêu cầu đăng nhập)
    Route::post('/register-package', [UserMembershipController::class, 'register'])->name('membership.register');

    // ─── HỆ THỐNG THÔNG BÁO ──────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/clear/read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');

    // ─── GIỎ HÀNG & THANH TOÁN (Chỉ dành cho người đã đăng nhập) ────────
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart/add/{id}', [CartController::class, 'add']);
    Route::patch('cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // ─── Lớp học (Bookings) ──────────────────────────────────────────
    Route::post('/classes/book', [BookingController::class, 'store'])->name('classes.store');
    Route::delete('/classes/cancel', [BookingController::class, 'cancel'])->name('classes.cancel');
}); // Kết thúc nhóm auth tại đây

// ─── ĐẶT LỊCH RIÊNG PT (CHO PHÉP KHÁCH XEM) ───────
Route::get('/booking-pt', [PtBookingController::class, 'index'])->name('booking.pt.index');
Route::get('/api/pt-booked-slots', [PtBookingController::class, 'getBookedSlots'])->name('api.pt.booked_slots');
Route::post('/booking-pt', [PtBookingController::class, 'store'])->middleware('auth')->name('booking.pt.store');

// Route lấy tất cả phản hồi cho trang chủ (Công khai)
Route::get('/api/all-reviews', [ReviewController::class, 'index'])->name('reviews.all');

// Route quên mật khẩu công khai
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetEmail'])->name('password.forgot.post');
// Route cập nhật body metric
Route::put('/body_metric/update', [BodyMetricController::class, 'update'])->name('metric.update');

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
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index')->name('products.index');
    Route::get('/search-products', 'getProductsApi'); // API tìm kiếm cho trang Shop
});

// ─── Lớp học (Bookings) ──────────────────────────────────────────────────────
Route::get('/classes',[BookingController::class, 'index'])->name('classes.index');

// Route công khai (Ai cũng xem được bài viết)
Route::get('/blog', [PostController::class, 'blog'])->name('posts.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('posts.show');

// Cấu hình route ảo để làm sạch lỗi của môi trường phát triển ngầm (IDX/Vite)
Route::put('/', function () {
    return response()->json(['status' => 'ok']);
});
