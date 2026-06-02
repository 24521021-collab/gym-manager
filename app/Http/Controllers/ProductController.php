<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product; // Nạp Model Product để lấy dữ liệu từ bảng products

class ProductController extends Controller
{
    /**
     * Tác dụng: Lấy tất cả dụng cụ tập gym từ Database và hiện ra trang danh sách */
    public function index(){
        // Chỉ trả về view, Fetch API trong JS sẽ lo phần lấy dữ liệu sản phẩm
        return view('shop');
    }
      // tìm kiếm sản phẩm 
    public function getProductsApi(Request $request){
    // 1. Tiếp nhận các tham số từ trình duyệt gửi lên qua URL (Query Strings)
    // Ví dụ: Nhận chuỗi "Whey" từ tham số ?search=Whey trên URL
    $search = $request->input('search');

    // 2. Khởi tạo đối tượng Query Builder từ Model Product
    // Nó chuẩn bị sẵn câu lệnh "SELECT * FROM products" nhưng chưa vội chạy xuống DB
    $query = Product::query();
    
    // 3. XỬ LÝ LOGIC TÌM KIẾM (Đoạn bạn đang thắc mắc)
    // Nếu người dùng có gõ chữ vào ô tìm kiếm (biến $search không bị rỗng)
    if ($search) {
        // Đính kèm thêm điều kiện lọc vào câu lệnh SQL. 
        // Toán tử 'like' kết hợp cặp dấu % giúp tìm kiếm tương đối (chứa từ khóa là được)
        $query->where('name', 'like', "%$search%");
    }
    
    // 4. LOGIC LỌC THEO DANH MỤC
    // Kiểm tra nếu trên URL có tham số 'category' VÀ giá trị của nó phải khác chữ 'all'
    if ($request->has('category') && $request->category != 'all') {
        // Tiếp tục nối thêm điều kiện: Cột danh mục phải bằng chính xác giá trị gửi lên (sups hoặc gear)
        $query->where('product_category', $request->category);
    }
    
    // 5. LOGIC PHÂN TRANG TỰ ĐỘNG
    // Laravel tự động tính toán số trang dựa trên tham số ?page=X trên URL.
    // Lấy đúng 8 bản ghi phù hợp và đóng gói chúng kèm theo các thông tin (tổng số trang, trang hiện tại...)
    $products = $query->paginate(8);
    
    // 6. TRẢ VỀ DỮ LIỆU JSON
    // Đóng gói mảng dữ liệu thành định dạng JSON chuẩn để hàm fetch() của JavaScript có thể đọc được
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