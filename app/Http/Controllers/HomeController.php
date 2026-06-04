<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GymPackage; 
use App\Models\BodyMetric;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

//1. Giải thích return view()
//Đây là lệnh để chỉ định Laravel sẽ mở file giao diện (HTML) nào để hiển thị cho người dùng.
//Cấu trúc: view('thư_mục.tên_file')
//Ví dụ của bạn: view('admin.packages.index')
//Laravel sẽ đi tìm file theo đường dẫn: resources/views/admin/packages/index.blade.php.
//Lưu ý: Trong Laravel, dấu chấm . thay thế cho dấu gạch chéo /.
//Giải thích compact('packages')
//Đây là một hàm của PHP (không chỉ riêng Laravel) dùng để đóng gói biến thành một cái hộp để gửi sang file giao diện.
//Cơ chế: Nó tìm cái biến có tên là $packages và tạo ra một mảng có từ khóa là 'packages'.
//Khi bạn viết:
//return view('admin.packages.index', compact('packages'));
//Nó tương đương với việc bạn nói: "Hãy mở file giao diện index.blade.php lên, và mang theo cái túi dữ liệu chứa biến $packages này sang đó cho tôi."
class HomeController extends Controller
{
    // Phương thức `index` sẽ xử lý logic để hiển thị trang chủ.
    public function index() {
        // Khởi tạo biến `$latestMetric` là null. Biến này sẽ lưu trữ chỉ số cơ thể mới nhất của người dùng.
        $latestMetric = null;
        // Kiểm tra xem người dùng đã đăng nhập hay chưa.
        if (Auth::check()) {
            // Nếu đã đăng nhập, tìm chỉ số cơ thể mới nhất của người dùng hiện tại.
            // `BodyMetric::where('user_id', Auth::id())`: Tìm tất cả các bản ghi BodyMetric có `user_id` trùng với ID của người dùng đang đăng nhập.
            // `orderBy('measured_at', 'desc')`: Sắp xếp các bản ghi theo thời gian đo giảm dần (mới nhất lên đầu).
            // `first()`: Lấy bản ghi đầu tiên (chính là bản ghi mới nhất).
            $latestMetric = BodyMetric::where('user_id', Auth::id())
                                      ->orderBy('measured_at', 'desc')
                                      ->first();
        }

        // Lấy 10 bài viết mới nhất để hiển thị ở khối Kiến thức trên trang chủ.
        // `Post::whereIn('status', ['Published', 'Sẵn sàng'])`: Lấy các bài viết có trạng thái là 'Published' hoặc 'Sẵn sàng'.
        // `latest()`: Sắp xếp các bài viết theo thời gian tạo giảm dần (mới nhất).
        // `take(10)`: Chỉ lấy 10 bài viết đầu tiên.
        // `get()`: Thực thi truy vấn và lấy về một tập hợp các đối tượng Post.
        $posts = Post::whereIn('status', ['Published', 'Sẵn sàng'])
                     ->latest()
                     ->take(10)
                     ->get();

        // Lấy tất cả các gói tập Gym và phân trang với 8 gói mỗi trang.
        $goiTaps = GymPackage::paginate(8);
        // Trả về view 'trangchu' và truyền các biến `$goiTaps`, `$latestMetric`, `$posts` sang view để hiển thị.
        return view('trangchu', compact('goiTaps', 'latestMetric', 'posts'));
    }  
}