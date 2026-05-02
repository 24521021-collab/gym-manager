<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GymPackage; 
class GoiTapController extends Controller
{
    public function index() {
        $goiTaps = GymPackage::all(); // 2. Ở đây chỉ cần gọi tên ngắn gọn
        return view('trangchu', compact('goiTaps'));
    }
}