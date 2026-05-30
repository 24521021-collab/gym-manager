<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết (Công khai)
     */
    public function index()
    {
        // Lấy bài viết mới nhất và phân trang
        $posts = Post::latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('posts.show', compact('post'));
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
            'header_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tạo slug tự động từ tiêu đề
        $data['slug'] = Str::slug($request->title);
        $data['author_id'] = auth()->id();

        // Xử lý upload ảnh bìa (nếu có)
        if ($request->hasFile('header_image')) {
            $data['header_image'] = $request->file('header_image')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Đăng bài viết thành công!');
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
        ]);

        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('header_image')) {
            // Xóa ảnh cũ nếu có
            if ($post->header_image) {
                Storage::disk('public')->delete($post->header_image);
            }
            $data['header_image'] = $request->file('header_image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Xóa bài viết
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Xóa ảnh trong storage trước khi xóa bài viết trong database
        if ($post->header_image) {
            Storage::disk('public')->delete($post->header_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết.');
    }

    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.edit', compact('post'));
    }
}
