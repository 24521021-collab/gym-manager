<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership; // Model lưu thông tin đăng ký
use App\Models\GymPackage; // Model chứa thông tin các gói tập
use App\Models\User;       // Model chứa thông tin người dùng
use Illuminate\Http\Request;
use Carbon\Carbon;         // Thư viện xử lý thời gian chuyên nghiệp

class AdminMembershipController extends Controller
{
    /**
     * Hàm hiển thị danh sách hội viên cho Admin
     */
    public function index(Request $request)
    {
        // 1. Lấy thời gian hiện tại
        $search = $request->input('search');
        $today = Carbon::now();

        // 2. TỰ ĐỘNG CẬP NHẬT: Quét Database, nếu ai đang 'Active' mà ngày hết hạn 
        // nhỏ hơn (trước) ngày hôm nay thì chuyển họ sang trạng thái 'Expired' (Hết hạn).
        Membership::where('status', 'Active')
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'Expired']);
        
        // 3. LẤY DANH SÁCH: 
        // - with(['user', 'package']): Kéo theo thông tin người dùng và gói tập để hiện tên
        // - paginate(10): Chỉ lấy 10 dòng mỗi trang để web chạy nhanh
        $query = Membership::with(['user', 'package']);

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $memberships = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        // 4. DỮ LIỆU ĐỔ VÀO MODAL: 
        // - Lấy danh sách User có quyền là 'member' để Admin chọn khi thêm mới
        $users = User::where('role', 'member')->select('id', 'full_name')->get();
        // - Lấy danh sách các gói tập để Admin chọn
        $packages = GymPackage::all();

        // 5. Trả dữ liệu ra file View
        return view('admin.members', compact('memberships','users','packages'));
    }

    /**
     * Hàm xử lý khi Admin tự tay thêm hội viên mới
     */
    public function store(Request $request)
    {
        // 1. KIỂM TRA DỮ LIỆU: Đảm bảo ID user và ID gói tập phải tồn tại trong DB
        $request->validate([
            'user_id' => 'required|exists:user,id',
            'package_id' => 'required|exists:gym_packages,id',
            'start_date' => 'required|date',
        ]);

        // 2. LẤY THÔNG TIN GÓI TẬP: Tìm gói tập mà Admin vừa chọn
        $package = GymPackage::findOrFail($request->package_id);
        
        // 3. XỬ LÝ NGÀY THÁNG: Chuyển ngày bắt đầu từ chữ sang kiểu dữ liệu Thời gian
        $startDate = Carbon::parse($request->start_date);
        
        // 4. TÍNH NGÀY HẾT HẠN: Lấy ngày bắt đầu cộng thêm số ngày quy định của gói đó
        $endDate = $startDate->copy()->addDays($package->duration_days);

        // 5. LƯU VÀO DATABASE: Tạo bản ghi mới trong bảng memberships
        Membership::create([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Active'
        ]);

        // 6. PHẢN HỒI: Quay lại trang trước và hiện thông báo thành công
        return redirect()->back()->with('success', 'Đã thêm hội viên mới thành công!');
    }

    /**
     * Hàm cập nhật trạng thái (ví dụ Admin muốn khoá thẻ hoặc kích hoạt lại)
     */
    public function update(Request $request, $id)
    {
        // 1. Kiểm tra trạng thái gửi lên có nằm trong danh sách cho phép không
        $request->validate([
            'status' => 'required|in:Active,Inactive,Expired,Cancelled'
        ]);

        // 2. Tìm hội viên theo ID, nếu không thấy sẽ báo lỗi 404
        $membership = Membership::findOrFail($id);
        
        // 3. Cập nhật trạng thái mới
        $membership->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái hội viên!');
    }

    /**
     * Hàm xoá vĩnh viễn một bản ghi đăng ký tập
     */
    public function destroy($id)
    {
        // 1. Tìm bản ghi cần xoá
        $membership = Membership::findOrFail($id);
        
        // 2. Thực hiện lệnh xoá khỏi Database
        $membership->delete();

        // 3. Trả về kết quả kiểu JSON để JavaScript (AJAX) xử lý xoá hàng trên giao diện mà không load lại trang
        return response()->json(['success' => true, 'message' => 'Đã xoá hội viên thành công']);
    }
}