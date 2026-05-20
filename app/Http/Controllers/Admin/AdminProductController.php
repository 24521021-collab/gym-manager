<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Trả về View nếu load trang, trả về JSON nếu Fetch API gọi lên.
     */
    public function index(Request $request)
    {
        // Kiểm tra nếu là request ngầm Fetch API gửi lên
        if ($request->ajax() || $request->wantsJson()) {
            $query = Product::query();
            // Tìm kiếm theo tên hoặc SKU
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%');
                });
            }
            // Sắp xếp sản phẩm mới nhất lên đầu và lấy dữ liệu dạng mảng sạch
            $products = $query->orderBy('id', 'desc')->paginate(10);   
            return response()->json($products);
        }
        // Nếu người dùng vào bằng trình duyệt thông thường, trả về khung giao diện
        return view('admin.products');
    }

    /**
     * Store a newly created resource in storage.
     * (Fetch API - Thêm mới sản phẩm)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $fileName);
            $data['image'] = $fileName;
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thêm sản phẩm thành công!',
            'data'    => $product
        ], 201);
    }
    /**
     * Update the specified resource in storage.
     * (Fetch API - Chỉnh sửa sản phẩm dùng Route Model Binding)
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có trong thư mục public
            if ($product->image && File::exists(public_path('images/products/' . $product->image))) {
                File::delete(public_path('images/products/' . $product->image));
            }
            
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $fileName);
            $data['image'] = $fileName;
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data'    => $product
        ]);
    }
    /**
     * Remove the specified resource from storage.
     * (Fetch API - Xóa sản phẩm)
     */
    public function destroy(Product $product)
    {
        if ($product->image && File::exists(public_path('images/products/' . $product->image))) {
            File::delete(public_path('images/products/' . $product->image));
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm thành công!'
        ]);
    }
}