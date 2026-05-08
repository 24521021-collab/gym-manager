<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Nạp Model Product để lấy dữ liệu từ bảng products

class ProductController extends Controller
{
    /**
     * Tác dụng: Lấy tất cả dụng cụ tập gym từ Database và hiện ra trang danh sách
     */
    public function index()
    {
        // Bước 1: Lấy toàn bộ danh sách sản phẩm từ bảng 'products'
        $products = Product::paginate(9);
        // Bước 2: Trả về file giao diện và gửi kèm danh sách sản phẩm qua biến $products
        return view('shop', compact('products'));
    }
      // tìm kiếm sản phẩm 
    public function getProductsApi(Request $request){
    $search = $request->input('search'); // Lấy từ khóa từ JS gửi lên

    // Tìm kiếm sản phẩm theo tên
    $products = Product::where('name', 'like', "%$search%")
                        ->paginate(10);

    // Trả về JSON (Đúng chuẩn mà hàm fetch mong đợi)
    return response()->json([
        'products' => $products
        ]);
    }
}