<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Nạp Model Product để lấy dữ liệu từ bảng products

class ProductController extends Controller
{
    /**
     * Tác dụng: Lấy tất cả dụng cụ tập gym từ Database và hiện ra trang danh sách */
    public function index(){
        // Bước 1: Lấy toàn bộ danh sách sản phẩm từ bảng 'products'
        $products = Product::paginate(9);
        // Bước 2: Trả về file giao diện và gửi kèm danh sách sản phẩm qua biến $products
        return view('shop', compact('products'));
    }
      // tìm kiếm sản phẩm 
    public function getProductsApi(Request $request){
    // 1. Tiếp nhận các tham số từ trình duyệt gửi lên qua URL (Query Strings)
    $search = $request->input('search');      // Từ khóa tìm kiếm tên sản phẩm
    $minPrice = $request->input('min_price');  // Giá tối thiểu (sàn)
    $maxPrice = $request->input('max_price');  // Giá tối đa (trần)
    // 2. Khởi tạo đối tượng Query Builder từ Model Product
    // Việc dùng query() giúp chúng ta có thể nối thêm các điều kiện WHERE một cách linh hoạt
    $query = Product::query();
    // 3. Nếu có từ khóa tìm kiếm, thêm điều kiện tìm theo tên (LIKE %...%)
    if ($search) {
        $query->where('name', 'like', "%$search%");
        }
    // 4. Nếu người dùng nhập giá tối thiểu, thêm điều kiện >=
    if ($minPrice) {
        $query->where('price', '>=', $minPrice);
        }
    // 5. Nếu người dùng nhập giá tối đa, thêm điều kiện <=
    if ($maxPrice) {
        $query->where('price', '<=', $maxPrice);
        }
    // 6. Thực hiện truy vấn và phân trang (10 sản phẩm/trang)
    $products = $query->paginate(10);
    // 7. Trả về dữ liệu dưới dạng JSON để JavaScript có thể xử lý và vẽ lại giao diện
    return response()->json([
        'products' => $products
        ]);
    }
    public function store(Request $request){
    $data = $request->all();
    if ($request->hasFile('image')) {
    $file = $request->file('image');
    // Lấy đuôi file (jpg, png, webp...)
    $ext = $file->getClientOriginalExtension();
    // Tạo tên file mới để không bao giờ bị trùng
    $newName = 'product_'.time() . '.' . $ext;
    // Lưu file vào folder public/images/products
    $file->move(public_path('images/products'), $newName);
        }
    }
}