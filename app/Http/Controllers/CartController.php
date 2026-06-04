<?php

namespace App\Http\Controllers;
use App\Models\GymClass;
use App\Models\GymPackage;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(){
        // Lấy dữ liệu từ Session có tên là 'cart'.
        // Nếu Session này chưa có gì (khách chưa mua), thì trả về mảng rỗng []
        $cart = session()->get('cart',[]);
        // Trả về file giao diện (view) nằm ở resources/views/cart/index.blade.php
        // compact('cart') giúp chuyển biến $cart sang bên file giao diện để hiển thị
        return view('cart',compact('cart'));
    }
    /**
     * Thêm sản phẩm/gói tập/lớp học vào giỏ hàng session.
     * @param Request $request, $id
     */
    public function add(Request $request, $id){
        $request->validate([
            'type' => 'required|string|in:product,package,class',
        ]);
        
        $type = $request->type;
        $rowId = $type . '_' . $id; // Tạo key duy nhất cho item trong giỏ hàng
        $cart = session()->get('cart', []);
        $itemDetails = [];
        $quantity = 1; // Mặc định số lượng là 1
        switch ($type) {
            case 'product':
                $product = Product::findOrFail($id);
                // Kiểm tra số lượng tồn kho
                if ($product->stock_quantity < 1) {
                    return response()->json(['error' => 'Sản phẩm này đã hết hàng!'], 400);
                }
                // Nếu sản phẩm đã có trong giỏ, tăng số lượng
                if (isset($cart[$rowId])) {
                    $quantity = $cart[$rowId]['quantity'] + 1;
                    if ($quantity > $product->stock_quantity) {
                        return response()->json(['error' => 'Kho chỉ còn ' . $product->stock_quantity . ' sản phẩm!'], 400);
                    }
                }
                $itemDetails = [
                    "row_id"   => $rowId,
                    "item_id"   => $product->id,
                    "item_type" => "product",
                    "name"     => $product->name,
                    "price"    => $product->price,
                    "quantity" => $quantity,
                    "image"    => $product->image ?? null,
                    "stock_quantity" => $product->stock_quantity // Lưu stock để kiểm tra sau này
                    ];
                break;
                case 'package':
                $package = GymPackage::findOrFail($id);

                // 1. Kiểm tra xem trong giỏ hàng đã có gói tập nào chưa (Mỗi lần chỉ được đăng ký 1 gói)
                foreach ($cart as $item) {
                    if ($item['item_type'] === 'package') {
                        return response()->json(['success' => false, 'error' => 'Bạn chỉ có thể đăng ký một gói tập trong mỗi đơn hàng!'], 400);
                    }
                }

                // 2. Kiểm tra xem gói tập cụ thể này đã có trong giỏ hàng chưa
                if (isset($cart[$rowId])) {
                    return response()->json(['success' => false, 'error' => 'Gói tập này đã có trong giỏ hàng!'], 400);
                }


                // 1. Kiểm tra nếu người dùng có gói tập đang Active (Chỉ cho phép 1 gói hiệu lực tại một thời điểm)
                $hasActive = false;
                if (Auth::check()) {
                    $hasActive = \App\Models\Membership::where('user_id', Auth::id())
                        ->where('status', 'Active')
                        ->whereDate('end_date', '>=', now())
                        ->exists();
                }
                
                if ($hasActive) {
                    return response()->json(['success' => false, 'error' => 'Bạn đang có một gói tập còn hiệu lực. Chỉ có thể đăng ký gói mới khi gói cũ hết hạn!'], 400);
                }

                $itemDetails = [
                    "row_id"   => $rowId,
                    "item_id"   => $package->id,
                    "item_type" => "package",
                    "name"     => $package->package_name,
                    "price"    => $package->price,
                    "quantity" => 1, // Gói tập luôn là 1
                    "image"    => null, // Gói tập thường không có ảnh riêng
                ];
                break;
            case 'class':
                $gymClass = GymClass::findOrFail($id);
                // Lớp học chỉ cho phép đăng ký 1 lần
                if (isset($cart[$rowId])) {
                    return response()->json(['error' => 'Lớp học này đã có trong giỏ hàng!'], 400);
                }
                $itemDetails = [
                    "row_id"   => $rowId,
                    "item_id"   => $gymClass->id,
                    "item_type" => "class",
                    "name"     => $gymClass->name,
                    "price"    => $gymClass->price,
                    "quantity" => 1, // Lớp học luôn là 1
                    "image"    => $gymClass->image ?? null,
                ];
                break;
        }
        $cart[$rowId] = $itemDetails;
        session()->put('cart', $cart);

        // Tính toán tổng tiền & tổng số lượng món đồ thực tế
        $total = 0;
        $cartCount = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity']; // Đếm tổng số lượng thay vì đếm số dòng mặt hàng
        }

        // Chỉ giữ lại một lệnh return duy nhất và trả về đầy đủ thông tin cần thiết
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng thành công!',
            'row_id' => $rowId,
            'item' => $itemDetails, 
            'total' => number_format($total) . 'đ',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Cập nhật số lượng item trong giỏ hàng.
     * @param Request $request Gồm 'row_id' và 'quantity'
     */
    public function update(Request $request){
        $request->validate([
            'row_id'   => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);
        $cart = session()->get('cart');
        $rowId = $request->row_id;
        $newQuantity = $request->quantity;

        if (!isset($cart[$rowId])) {
            return response()->json(['error' => 'Item không tồn tại trong giỏ hàng!'], 404);
        }

        $itemType = $cart[$rowId]['item_type'];
        $itemId   = $cart[$rowId]['item_id'];

        // Xử lý logic số lượng dựa trên loại mặt hàng
        if ($itemType === 'product') {
            $product = Product::find($itemId);
            if (!$product) {
                return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
            }
            if ($newQuantity > $product->stock_quantity) {
                return response()->json(['error' => 'Kho chỉ còn ' . $product->stock_quantity . ' sản phẩm!'], 400);
            }
            $cart[$rowId]['quantity'] = $newQuantity;
        } elseif ($itemType === 'package' || $itemType === 'class') {
            // Gói tập và lớp học luôn có số lượng là 1, không cho phép thay đổi
            if ($newQuantity !== 1) {
                return response()->json(['error' => 'Không thể thay đổi số lượng cho ' . $itemType . '!'], 400);
            }
            $cart[$rowId]['quantity'] = 1;
        }

        session()->put('cart', $cart);
        // Tính toán lại tổng tiền
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return response()->json([
            'success'  => true,
            'subtotal' => number_format($cart[$rowId]['price'] * $cart[$rowId]['quantity']) . 'đ',
            'total'    => number_format($total) . 'đ',
        ]);
    }

    /**
     * Xóa item khỏi giỏ hàng.
     * @param Request $request Gồm 'row_id'
     */
    public function remove(Request $request){
        // 1. Validate dữ liệu đầu vào: Đảm bảo 'row_id' được gửi lên và là kiểu chuỗi.
        // 'row_id' là định danh duy nhất của mỗi mặt hàng trong giỏ hàng (ví dụ: product_1, package_5).
        $request->validate([
            'row_id' => 'required|string', // Yêu cầu 'row_id' phải có và là chuỗi.
        ]);

        // 2. Lấy giỏ hàng hiện tại từ session.
        // Nếu session 'cart' chưa tồn tại, nó sẽ trả về một mảng rỗng.
        $cart = session()->get('cart');

        
        // 3. Lấy 'row_id' của mặt hàng cần xóa từ request.
        $rowId = $request->row_id;

        // 4. Kiểm tra xem mặt hàng có 'row_id' tương ứng có tồn tại trong giỏ hàng không.
        if (isset($cart[$rowId])) {
            // 5. Nếu tồn tại, xóa mặt hàng đó khỏi mảng giỏ hàng.
            unset($cart[$rowId]);
            // 6. Cập nhật lại giỏ hàng trong session với mảng đã xóa mặt hàng.
            session()->put('cart', $cart);
        }

        // 7. Tính toán lại tổng tiền của giỏ hàng sau khi xóa.
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // 8. Trả về phản hồi JSON cho client (thường là JavaScript ở frontend).
        return response()->json([
            'message'    => 'Đã xóa sản phẩm thành công!', // Thông báo thành công.
            'total'      => number_format($total) . 'đ', // Tổng tiền mới của giỏ hàng.
            'cart_count' => count($cart), // Tổng số mặt hàng (dòng) còn lại trong giỏ hàng.
            'cart_data'  => $cart, // Dữ liệu giỏ hàng đã cập nhật (tùy chọn, có thể không cần thiết nếu frontend tự quản lý).
        ]);
    }
}



//exp
$cart = [
    "class_15" => [
        "row_id" => "class_15",
        "item_id" => 15,
        "item_type" => "class",
        "price" => 500000,
        "quantity" => 1
    ],
    "product_5" => [
        "row_id" => "product_5",
        "item_id" => 5,
        "item_type" => "product",
        "price" => 100000,
        "quantity" => 1  
    ]];//