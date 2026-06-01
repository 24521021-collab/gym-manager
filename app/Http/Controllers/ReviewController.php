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
                  ->where('payment_status',['Pending', 'Paid']);
        })->distinct()->get(['id', 'name']); // distinct() để không bị lặp nếu mua 1 món nhiều lần

        // 2. Lấy danh sách PT:
        // Tìm trong bảng User những người có vai trò là 'pt'
        // và người dùng hiện tại (customer_id) đã từng có lịch đặt (ptBookingsAsPt) với họ.
        $pts = User::where('role', 'pt')
            ->whereHas('ptBookingsAsPt', function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                      ->where('status', ['pending','paid']); 
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
        // 1. Strict Validation
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'reviewable_type' => 'required|string|in:product,pt',
            'reviewable_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $type = $validated['reviewable_type'];
        $id = $validated['reviewable_id'];

        // Map friendly names to actual model classes
        $modelMap = [
            'product' => Product::class,
            'pt' => User::class,
        ];
        $modelClass = $modelMap[$type];

        // 2. Anti-Spam & Business Rules Check
        if ($type === 'product') {
            $hasPurchased = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', $user->id)
                ->where('orders.payment_status','Pending')
                ->where('order_items.product_id', $id)
                ->exists();

            if (!$hasPurchased) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua và thanh toán thành công.'
                ], 403);
            }
        } elseif ($type === 'pt') {
            // Verify the target user is actually a PT
            $targetPT = User::where('id', $id)->where('role', 'pt')->exists();
            if (!$targetPT) {
                return response()->json(['success' => false, 'message' => 'HLV không tồn tại.'], 404);
            }

            // MOCK: Booking Check
            // In a real app: check if DB::table('bookings')->where('user_id', $user->id)->where('pt_id', $id)->exists();
            $hasBooked = true; 
            
            if (!$hasBooked) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bạn cần đăng ký với HLV này trước khi để lại đánh giá.'
                ], 403);
            }
        }

        // 3. Prevent Duplicate Submissions
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

        // 4. Create Review with Sanitization
        // We use strip_tags as an extra security layer against XSS on top of Blade/innerText
        try {
            $review = Review::create([
                'user_id' => $user->id,
                'reviewable_id' => $id,
                'reviewable_type' => $modelClass,
                'rating' => $validated['rating'],
                'comment' => strip_tags($validated['comment']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn! Đánh giá của bạn đã được ghi nhận hệ thống.',
                'data' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau.'], 500);
        }
    }
}
