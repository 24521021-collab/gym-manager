<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\PTProfile; // Giả sử bảng PT của bạn tên này
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');

        // Khởi tạo query với quan hệ pt và user
        $query = GymClass::with('pt.user');

        // Nếu có từ khóa tìm kiếm, lọc theo tên lớp, tên phòng hoặc tên PT
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('room_name', 'like', "%{$search}%")
                  ->orWhereHas('pt.user', function($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%");
                  });
        }

        $classes = $query->paginate(10);
        $pts = PTProfile::with('user')->get(); 
        return view('admin.classes', compact('classes', 'pts'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'pt_id' => 'required|exists:pt_profiles,id',
            'max_capacity' => 'required|numeric',
            'schedule_time' => 'required',
            'room_name' => 'required'
        ]);
        GymClass::create($data);
        return redirect()->route('admin.gym-classes.index')->with('success', 'Thêm mới thành công!');
    }

    public function update(Request $request, $id) {
        $class = GymClass::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'pt_id' => 'required|exists:pt_profiles,id',
            'max_capacity' => 'required|numeric',
            'schedule_time' => 'required',
            'room_name' => 'required'
        ]);
        $class->update($data);
        return redirect()->route('admin.gym-classes.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id) {
        GymClass::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa lớp học!');
    }
}