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
    $search = $request->input('search');

    // 2. Khởi tạo đối tượng Query Builder từ Model Product
    $query = Product::query();
    if ($search) {
        $query->where('name', 'like', "%$search%");
        }
    // Lọc theo danh mục (sups / gear) gửi lên từ JS
    if ($request->has('category') && $request->category != 'all') {
        $query->where('product_category', $request->category);
    }
    // Phân trang đồng bộ 8 sản phẩm/trang
    $products = $query->paginate(8);
    // 7. Trả về dữ liệu dưới dạng JSON để JavaScript có thể xử lý và vẽ lại giao diện
    return response()->json([
        'products' => $products
        ]);
    }
    // 
    public function store(Request $request){
        $request->validate([
        'name' => 'required',
        'sku' => 'required|unique:products',
        'product_category' =>'required',
        'price' => 'required|numeric',
        'stock_quantity' => 'required|integer|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Validate file ảnh
    ]);
    $data = $request->all();
    if ($request->hasFile('image')) {
    $file = $request->file('image');
    // Lấy đuôi file (jpg, png, webp...)
    $ext = $file->getClientOriginalExtension();
    // Tạo tên file mới để không bao giờ bị trùng
    $newName = 'product_'.time() . '.' . $ext;
    // Lưu file vào folder public/images/products
    $file->move(public_path('images/products'), $newName);
    $data['image'] = $newName;
        }
        Product::create($data);
        return redirect()->back()->with('success', 'Thêm sản phẩm thành công!');
    }
}