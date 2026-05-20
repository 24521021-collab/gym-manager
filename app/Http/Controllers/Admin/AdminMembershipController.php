<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership; // Model lưu thông tin đăng ký
use App\Models\GymPackage; // Model chứa thông tin các gói tập
use App\Models\User;       // Model chứa thông tin người dùng
use Illuminate\Http\Request;
use Carbon\Carbon;         // Thư viện xử lý thời gian chuyên nghiệp

class AdminMembershipController extends Controller{
    public function index(Request $request){
        $search = $request->input('search');
        $today = Carbon::now();
        // 1. TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI HẾT HẠN TRƯỚC KHI TRẢ VỀ DỮ LIỆU
        Membership::where('status', 'Active')
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'Expired']);
        // 2. XÂY DỰNG QUERY TÌM KIẾM THEO TÊN HOẶC EMAIL KHÁCH HÀNG
        $query = Membership::with(['user', 'package']);
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // 3. PHÂN TRANG (10 bản ghi một trang giống hệt product)
        $memberships = $query->orderBy('created_at', 'desc')->paginate(10);

        // NẾU LÀ REQUEST ASYNC TỪ FETCH API -> TRẢ VỀ JSON LUÔN
        if ($request->ajax()) {
            return response()->json($memberships);
        }
        // 4. LẤY DỮ LIỆU ĐỂ ĐỔ VÀO CÁC Ô SELECT BOX TRONG MODAL (Chỉ load lần đầu)
        $users = User::where('role', 'member')->select('id', 'full_name')->get();
        $packages = GymPackage::all();
        return view('admin.members', compact('memberships', 'users', 'packages'));
    }
    public function store(Request $request){
        $request->validate([
            'user_id' => 'required|exists:user,id', // Sửa lại thành 'user' theo đúng tên bảng trong DB của bạn
            'package_id' => 'required|exists:gym_packages,id',
            'start_date' => 'required|date',
        ]);
        $package = GymPackage::findOrFail($request->package_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays($package->duration_days);
        $membership = Membership::create([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Active'
        ]);

        return response()->json(['success' => true, 'data' => $membership]);
    }
    /**
     * Xử lý cập nhật thông tin và trạng thái hội viên bằng Fetch API
     */
    public function update(Request $request, $id){
        $request->validate([
            'user_id' => 'required|exists:user,id', // Sửa lại thành 'user'
            'package_id' => 'required|exists:gym_packages,id',
            'start_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Expired,Cancelled'
        ]);
        $membership = Membership::findOrFail($id);
        // Tính toán lại ngày hết hạn phòng trường hợp Admin đổi gói tập hoặc ngày bắt đầu
        $package = GymPackage::findOrFail($request->package_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays($package->duration_days);

        $membership->update([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $request->status
        ]);

        return response()->json(['success' => true, 'data' => $membership]);
    }
    /**
     * Xử lý xóa vĩnh viễn (Giữ nguyên cơ chế API cũ của bạn)
     */
    public function destroy($id){
        $membership = Membership::findOrFail($id);
        $membership->delete();

        return response()->json(['success' => true, 'message' => 'Đã xoá hồ sơ đăng ký tập thành công!']);
    }
}