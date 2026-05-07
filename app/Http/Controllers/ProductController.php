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
        $products = Product::all();

        // Bước 2: Trả về file giao diện và gửi kèm danh sách sản phẩm qua biến $products
        return view('shop', compact('products'));
    }
}