<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GymPackage; 

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
    // phần này chỉ hiện thị gói tập trên trang chủ 
    public function index() {
        $goiTaps = GymPackage::all(); // 2. Ở đây chỉ cần gọi tên ngắn gọn
        return view('trangchu', compact('goiTaps'));
    }  
}