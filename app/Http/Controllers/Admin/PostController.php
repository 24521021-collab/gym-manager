<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< Updated upstream
use Illuminate\Http\Request;

class PostController extends Controller
{
    //
}
=======
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Constructor bảo mật
    public function __construct()
    {
        // Ai cũng được xem danh sách/chi tiết
        $this->middleware('auth')->except(['index', 'show']);
        
        // Chỉ Admin mới được thực hiện các hành động sau
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
            }
            return $next($request);
        })->only(['store', 'update', 'destroy']);
    }

    // Xem danh sách bài viết
    public function index() {
        $posts = Post::latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    // Lưu bài viết mới (Chỉ Admin)
    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required',
        ]);
        
        $data['slug'] = Str::slug($request->title);
        $data['author_id'] = auth()->id();

        Post::create($data);
        return back()->with('success', 'Đã đăng bài viết!');
    }

    // Xóa bài viết (Chỉ Admin)
    public function destroy($id) {
        Post::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa bài viết.');
    }
}
>>>>>>> Stashed changes
