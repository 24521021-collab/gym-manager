<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PTProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết cho admin
     */
    public function index()
    {
        // Sửa lỗi: Gọi paginate trực tiếp từ Model. 
        // Thêm eager loading 'author' để tối ưu hiệu năng và sắp xếp mới nhất lên đầu.
        $posts = Post::with('author')->latest()->paginate(10);

        // Đảm bảo truyền biến số nhiều 'posts' để view nhận diện được
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

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
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

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết.');
    }

    public function create()
    {
        // Khởi tạo một đối tượng Post rỗng để truyền sang view
        $post = new Post(); 
        $authors = PTProfile::with('user')
            ->get()
            ->sortBy(fn($pt) => $pt->user->full_name);

        return view('admin.post_form', compact('post', 'authors'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $authors = PTProfile::with('user')
            ->get()
            ->sortBy(fn($pt) => $pt->user->full_name);

        return view('admin.post_form', compact('post', 'authors'));
    }
}
