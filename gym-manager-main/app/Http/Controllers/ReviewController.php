<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    // Trả về tất cả các đánh giá hiện có trên hệ thống
    public function index()
    {
        $reviews = Review::with(['user', 'reviewable'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

    /**
     * Hàm lấy danh sách các đối tượng (Sản phẩm/PT) mà người dùng hiện tại có quyền đánh giá.
     * Điều này giúp ngăn chặn việc đánh giá "rác" hoặc đánh giá khống.
     */
    public function getReviewableTargets(Request $request)
    {
        // Lấy ID của người dùng đang đăng nhập
        $userId = Auth::id();

        // 1. Lấy danh sách sản phẩm: 
        // Chỉ lấy những sản phẩm xuất hiện trong các đơn hàng (Order) của người dùng này
        // và đơn hàng đó phải có trạng thái thanh toán là Chờ xử lý hoặc Đã thanh toán.
    
        $products = Product::whereHas('orderItems.order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('payment_status', ['Pending', 'Paid']);
        })->distinct()->get(['id', 'name']); // distinct() để không bị lặp nếu mua 1 món nhiều lần

        // 2. Lấy danh sách PT:
        // Tìm trong bảng User những người có vai trò là 'pt'
        // và người dùng hiện tại (customer_id) đã từng có lịch đặt (ptBookingsAsPt) với họ.
        $pts = User::where('role', 'pt')
            ->whereHas('ptBookingsAsPt', function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                      ->whereIn('status', ['pending', 'paid']); 
            })->distinct()->get(['id', 'full_name']);

        // Trả về dữ liệu dạng JSON để JavaScript ở Frontend (trang chủ) có thể nhận và hiển thị lên ô Select
        return response()->json([
            'success' => true,
            'products' => $products,
            'pts' => $pts
        ]);
    }

    public function store(Request $request)
    {
        // 1. Strict Validation (Xác thực dữ liệu đầu vào nghiêm ngặt)
        // Đảm bảo dữ liệu gửi lên đầy đủ và đúng định dạng trước khi xử lý.
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5', // Điểm đánh giá từ 1-5 sao
            'comment' => 'required|string|max:1000',    // Bình luận tối đa 1000 ký tự
            'reviewable_type' => 'required|string|in:product,pt', // Chỉ cho phép đánh giá SP hoặc PT
            'reviewable_id' => 'required|integer',      // ID của đối tượng
        ]);

        $user = Auth::user();
        $type = $validated['reviewable_type'];
        $id = $validated['reviewable_id'];

        // Ánh xạ tên loại (alias) sang Class Model thực tế để lưu vào quan hệ Polymorphic
        $modelMap = [
            'product' => Product::class,
            'pt' => User::class,
        ];
        $modelClass = $modelMap[$type];

        // 2. Anti-Spam & Business Rules Check (Kiểm tra chống spam và quy tắc nghiệp vụ)
        if ($type === 'product') {
            // Kiểm tra xem khách hàng đã mua sản phẩm này chưa qua các đơn hàn
            /**
             * Giải thích truy vấn DB bên dưới:
             * - DB::table('orders'): Truy cập trực tiếp vào bảng 'orders'.
             * - join('order_items', ...): Kết nối với bảng 'order_items'.
             *   (Ở đây dùng dấu gạch dưới vì Query Builder làm việc trực tiếp với tên bảng DB).
             * - 'orders.id': Cột ID (khóa chính) của bảng đơn hàng.
             * - 'order_items.order_id': Cột liên kết (khóa ngoại) trong bảng chi tiết để biết món hàng thuộc đơn nào.
             * - where('order_items.product_id', $id): Kiểm tra sản phẩm đang đánh giá có nằm trong các đơn này không.
             */
            $hasPurchased = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', $user->id)
                // Chấp nhận cả trạng thái đang chờ xử lý và đã thanh toán
                ->whereIn('orders.payment_status', ['Pending', 'Paid'])
                ->where('order_items.product_id', $id)
                ->exists();

            if (!$hasPurchased) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua.'
                ], 403);
            }
            
        } elseif ($type === 'pt') {
            // Kiểm tra xem ID người dùng mục tiêu có thực sự là Huấn luyện viên (PT) không
            $targetPT = User::where('id', $id)->where('role', 'pt')->exists();
            if (!$targetPT) {
                return response()->json(['success' => false, 'message' => 'HLV không tồn tại.'], 404);
            }

            // Kiểm tra lịch đặt PT thực tế trong bảng pt_bookings
            $hasBooked = DB::table('pt_bookings')
                ->where('customer_id', $user->id)
                ->where('pt_id', $id)
                ->whereIn('status', ['pending', 'paid', 'confirmed'])
                ->exists();
            
            if (!$hasBooked) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bạn cần có lịch tập với HLV này trước khi để lại đánh giá.'
                ], 403);
            }
        }

        // 3. Prevent Duplicate Submissions (Chống đánh giá lặp lại)
        // Mỗi người dùng chỉ được đánh giá một lần duy nhất cho mỗi đối tượng (Sản phẩm hoặc PT)
        $exists = Review::where('user_id', $user->id)
            ->where('reviewable_id', $id)
            ->where('reviewable_type', $modelClass)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false, 
                'message' => 'Bạn đã gửi đánh giá cho mục này rồi.'
            ], 422);
        }

        // 4. Create Review with Sanitization (Lưu đánh giá và làm sạch dữ liệu)
        try {
            $review = Review::create([
                'user_id' => $user->id,
                'reviewable_id' => $id,
                'reviewable_type' => $modelClass,
                'rating' => $validated['rating'],
                // strip_tags để loại bỏ các thẻ HTML/Script nguy hiểm chống tấn công XSS
                'comment' => strip_tags($validated['comment']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn! Đánh giá của bạn đã được ghi nhận vào hệ thống.',
                'data' => $review
            ], 201);

        } catch (\Exception $e) {
            // Ghi log lỗi nếu cần thiết và trả về lỗi hệ thống
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau.'], 500);
        }
    }
}
