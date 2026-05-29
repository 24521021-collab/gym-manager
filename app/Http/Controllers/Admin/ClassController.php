<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\PTProfile; // Giả sử bảng PT của bạn tên này
use Illuminate\Support\Facades\File;
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
                  ->orWhereHas('pt.user', function($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%");
                  });
        }

        $classes = $query->paginate(10);

        // Trả về JSON nếu là request AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($classes)->header('Vary', 'X-Requested-With');
        }

        $pts = PTProfile::with('user')->get(); 
        return view('admin.classes', compact('classes', 'pts'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'pt_id' => 'required|exists:pt_profiles,id',
            'max_capacity' => 'required|numeric',
            'description' => 'nullable|string',
            'total_sessions' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/classes'), $imageName);
            $data['image'] = $imageName;
        }

        $class = GymClass::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thêm lớp học mới thành công!',
            'data' => $class
        ]);
    }

    public function update(Request $request, $id) {
        $class = GymClass::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'pt_id' => 'required|exists:pt_profiles,id',
            'max_capacity' => 'required|numeric',
            'description' => 'nullable|string',
            'total_sessions' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except(['image', '_method']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu tồn tại để tránh rác server
            if ($class->image && File::exists(public_path('uploads/classes/' . $class->image))) {
                File::delete(public_path('uploads/classes/' . $class->image));
            }
            
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/classes'), $imageName);
            $data['image'] = $imageName;
        }

        $class->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật lớp học thành công!',
            'data' => $class
        ]);
    }

    public function destroy($id) {
        $class = GymClass::findOrFail($id);
        // Xóa ảnh khi xóa record
        if ($class->image && File::exists(public_path('uploads/classes/' . $class->image))) {
            File::delete(public_path('uploads/classes/' . $class->image));
        }
        $class->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa lớp học!']);
    }
}