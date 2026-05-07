<?php

namespace App\Http\Controllers;
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
    public function add($id){
        // Dùng ID từ đường dẫn để tìm sản phẩm trong DB. 
        // Nếu ID bậy bạ không có trong DB, hàm này sẽ báo lỗi 404 ngay
        $product = Product::findOrFail($id);
        // KIỂM TRA: Nếu cột stock_quantity trong DB <= 0 thì thông báo hết hàng
        if ($product->stock_quantity < 1) {
            return redirect()->back()->with('error', 'Sản phẩm này đã cháy hàng!');
        }
        // Lấy giỏ hàng hiện tại trong máy người dùng ra (Session)
        $cart = session()->get('cart', []);
        // KIỂM TRA: Sản phẩm này đã có trong giỏ chưa? (Dựa vào ID làm chìa khóa)
        if(isset($cart[$id])) {
            // Nếu có rồi, kiểm tra xem nếu cộng thêm 1 thì có lố số lượng trong kho không
            if ($cart[$id]['stock_quantity'] + 1 > $product->stock_quantity) {
                return redirect()->back()->with('error', 'Kho chỉ còn ' . $product->stock_quantity . ' sản phẩm!');
            }
            // Nếu ổn, tăng số lượng của sản phẩm đó trong giỏ lên 1
            $cart[$id]['stock_quantity']++;
        } else {
            // Nếu sản phẩm CHƯA có trong giỏ, tạo mới một "món hàng" với các thông tin từ DB
            $cart[$id] = [
                "name"     => $product->name,
                "sku"      => $product->sku,
                "stock_quantity" => 1,
                "price"    => $product->price,
            ];
        }
        // Lưu lại cái mảng giỏ hàng đã thay đổi vào lại Session
        // Giải thích: "Cất danh sách đồ khách vừa chọn vào bộ nhớ tạm (Session) của máy chủ để khi khách chuyển trang khác, món hàng vẫn còn nằm trong giỏ."
        session()->put('cart', $cart);
        // Đưa người dùng quay lại trang trước đó kèm dòng thông báo xanh (success)
        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng thành công!');
    }
    public function update(Request $request){
    // 1. Validate (Cũng đổi tên biến kiểm tra thành stock_quantity)
    $request->validate([
        'id' => 'required|exists:products,id',
        'stock_quantity' => 'required|integer|min:1|max:99', 
    ]);
    if($request->id && $request->stock_quantity) {
        $cart = session()->get('cart');
        $product = Product::find($request->id);
        if($request->stock_quantity > $product->stock_quantity) {
            return response()->json(['error' => 'Kho không đủ!'], 400);
        }
        // Cập nhật Session
        $cart[$request->id]["stock_quantity"] = $request->stock_quantity;
        session()->put('cart', $cart);
        // TÍNH TOÁN GIÁ TRỊ MỚI ĐỂ TRẢ VỀ
        $subtotal = number_format($cart[$request->id]['price'] * $cart[$request->id]['stock_quantity']) . 'đ';
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['stock_quantity'];
        }
        $total_formatted = number_format($total) . 'đ';
        return response()->json([
            'success' => true,
            'subtotal' => $subtotal,        // Thành tiền của 1 món
            'total' => $total_formatted     // Tổng tiền cả giỏ hàng
        ]);
         }
    }
    public function remove(Request $request){
        // Kiểm tra xem phía giao diện có gửi cái ID nào lên không
        if($request->id) {
            // Lấy giỏ hàng ra
            $cart = session()->get('cart');
            // Nếu tìm thấy cái ID đó trong giỏ hàng
            if(isset($cart[$request->id])) {
                // Dùng hàm unset để "đào thải" món đó ra khỏi mảng
                unset($cart[$request->id]);
                // Lưu giỏ hàng mới (đã xóa món kia) vào lại Session
                session()->put('cart', $cart);
            }
            // Trả về thông báo thành công (thường dùng cho Ajax)
            return response()->json(['success' => 'Đã xóa sản phẩm khỏi giỏ!']);
        }
    }
}
