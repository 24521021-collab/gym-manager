<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GymPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class GymPackageController extends Controller{
    /**
     * CHỨC NĂNG: HIỂN THỊ DANH SÁCH
     * Trả về trang quản lý với danh sách các gói tập đã phân trang.
     */
    public function index(Request $request){
        $search = $request->input('search');
        $query = GymPackage::query();
        if ($search) {
            $query->where('package_name', 'like', "%{$search}%");
        }
        $packages = $query->orderBy('id', 'desc')->paginate(10);

        // Nếu là request AJAX, trả về JSON
        if ($request->ajax()) {
            return response()->json($packages);
        }
        return view('admin.packages', compact('packages'));
}

    /**
     * CHỨC NĂNG: LƯU MỚI
     * Kiểm tra dữ liệu và tạo gói tập mới.
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validate)
        $request->validate([
            'package_name' => 'required|string|max:255|unique:gym_packages,package_name',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // 2. Xử lý dữ liệu
        $data = $request->all();
        $data['slug'] = Str::slug($request->package_name);

        // 3. Lưu vào Database
        $package = GymPackage::create($data);
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm gói tập mới thành công!',
            'data' => $package
        ], 201);
    }

    /**
     * CHỨC NĂNG: CẬP NHẬT
     * Dùng GymPackage $package thay vì $id. Laravel sẽ tự tìm bản ghi tương ứng.
     */
    public function update(Request $request, GymPackage $package){
        // 1. Validate dữ liệu
        // Lưu ý: unique phải loại trừ ID hiện tại: 'unique:bảng,cột,ID_ngoại_lệ'
        $request->validate([
            'package_name' => 'required|string|max:255|unique:gym_packages,package_name,' . $package->id,
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // 2. Cập nhật Slug nếu tên thay đổi
        $data = $request->all();
        $data['slug'] = Str::slug($request->package_name);
        // 3. Lưu thay đổi
        $package->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'data' => $package
        ]);
    }

    /**
     * CHỨC NĂNG: XÓA
     * Dùng GymPackage $package để tự động kiểm tra sự tồn tại của gói tập.
     */
    public function destroy(GymPackage $package)
    {
        // Kiểm tra xem có hội viên nào đang sử dụng gói này không
        if ($package->memberships()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa! Gói này đang có hội viên đăng ký.'
            ], 400);
        }

        // Thực hiện xóa
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa gói tập thành công!'
        ]);
    }
}