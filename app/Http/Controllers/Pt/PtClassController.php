<?php

namespace App\Http\Controllers\Pt;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PtClassController extends Controller
{
    // 1. Lấy danh sách (Phân trang & Fetch API)
    public function index(Request $request)
    {
        $ptId = auth()->user()->ptProfile->id;
        if ($request->ajax()) {
            // Tăng hiệu năng bằng cách chỉ lấy các cột cần thiết và load quan hệ học viên
            $classes = GymClass::where('pt_id', $ptId)
                ->with(['bookings.user:id,full_name,email'])
                ->paginate(5);

            return response()->json($classes);
        }
        return view('pt.classes');
    }

    // 2. Lưu mới
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_capacity' => 'required|integer|min:1',
            'total_sessions' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');
        $data['pt_id'] = auth()->user()->ptProfile->id;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $data['image'] = $filename;
        }

        $class = GymClass::create($data);
        
        return response()->json(['success' => true, 'data' => $class->load('bookings.user')]);
    }

    // 3. Cập nhật
    public function update(Request $request, $id)
    {
        $class = GymClass::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_capacity' => 'required|integer|min:1',
            'total_sessions' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['image', '_method']);
        
        if ($request->hasFile('image')) {
            if ($class->image && file_exists(public_path('uploads/classes/' . $class->image))) {
                unlink(public_path('uploads/classes/' . $class->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $data['image'] = $filename;
        }

        $class->update($data);
        return response()->json(['success' => true, 'data' => $class->load('bookings.user')]);
    }

    // 4. Xóa
    public function destroy($id)
    {
        $class = GymClass::findOrFail($id);
        if ($class->image && file_exists(public_path('uploads/classes/' . $class->image))) {
            unlink(public_path('uploads/classes/' . $class->image));
        }
        $class->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa lớp học thành công']);
    }
}
