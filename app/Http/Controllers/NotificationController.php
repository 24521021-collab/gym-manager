<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Hiển thị danh sách thông báo của chính người dùng đó
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('notifications', compact('notifications'));
    }

    // Đánh dấu tất cả là đã đọc
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Đã đánh dấu đọc toàn bộ.']);
    }

    // Xóa một thông báo cụ thể (Validate bảo mật: chỉ xóa của mình)
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa thông báo.']);
    }

    // Xóa tất cả thông báo đã đọc (Dọn dẹp hộp thư)
    public function clearRead()
    {
        Notification::where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Đã dọn dẹp các thông báo cũ.');
    }
}
