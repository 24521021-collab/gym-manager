<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PtProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết cho admin
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Post::with('author')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10);

        // Trả về JSON nếu là request AJAX (từ Fetch API)
        if ($request->ajax()) {
            return response()->json($posts);
        }

        return view('admin.posts', compact('posts'));
    }

    /**
     * Hiển thị danh sách bài viết cho khách (Blog công khai)
     */
    public function blog()
    {
        $posts = Post::with('author')
                    ->whereIn('status', ['Published', 'Sẵn sàng'])
                    ->latest()
                    ->paginate(10); // Đổi từ 9 thành 10 theo yêu cầu
        return view('posts', compact('posts'));
    }

    public function show($slug)
    {
        // Chuyển bài viết đơn lẻ thành Collection để View posts.blade.php (vốn dùng @foreach) 
        // có thể hiển thị bài viết đó mà không bị lỗi crash trang.
        $post = Post::with('author')->where('slug', $slug)->firstOrFail();
        $posts = collect([$post]);
        return view('posts', compact('posts')); 
    }

    /**
     * Lưu bài viết mới (Chỉ dành cho Admin)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required',
            'author_id' => 'required|exists:user,id',
            'header_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Draft,Sẵn sàng',
        ]);

        // Tạo slug tự động từ tiêu đề
        $data['slug'] = Str::slug($request->title);

        // Xử lý upload ảnh bìa (nếu có)
        if ($request->hasFile('header_image')) {
            $file = $request->file('header_image');
            $imageName = time() . '.' . $file->extension();
            $file->move(public_path('images/posts'), $imageName);
            $data['header_image'] = $imageName;
        }

        $post = Post::create($data);

        return response()->json(['success' => true, 'message' => 'Đăng bài viết thành công!', 'data' => $post]);
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required',
            'author_id' => 'required|exists:user,id',
            'status' => 'required|in:Draft,Sẵn sàng',
        ]);

        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('header_image')) {
            // Xóa ảnh cũ nếu có
            if ($post->header_image && File::exists(public_path('images/posts/' . $post->header_image))) {
                File::delete(public_path('images/posts/' . $post->header_image));
            }
            $file = $request->file('header_image');
            $imageName = time() . '.' . $file->extension();
            $file->move(public_path('images/posts'), $imageName);
            $data['header_image'] = $imageName;
        }

        $post->update($data);

        return response()->json(['success' => true, 'message' => 'Cập nhật bài viết thành công!', 'data' => $post]);
    }

    /**
     * Xóa bài viết
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Xóa ảnh trong thư mục public/images/posts
        if ($post->header_image && File::exists(public_path('images/posts/' . $post->header_image))) {
            File::delete(public_path('images/posts/' . $post->header_image));
        }

        $post->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa bài viết thành công!']);
    }

    public function create()
    {
        // Khởi tạo một đối tượng Post rỗng để truyền sang view
        $post = new Post(); 
        $authors = PtProfile::with('user')
            ->get()
            ->sortBy(fn($pt) => $pt->user->full_name);

        return view('admin.post_form', compact('post', 'authors'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $authors = PtProfile::with('user')
            ->get()
            ->sortBy(fn($pt) => $pt->user->full_name);

        return view('admin.post_form', compact('post', 'authors'));
    }
}
