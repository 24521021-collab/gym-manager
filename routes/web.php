<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoiTapController;
use App\Http\Controllers\RegisterController;


// 1. Trang chủ (hiện chữ chào mừng hoặc file welcome có sẵn)

//home( trang chủ )
Route::get('/',[GoiTapController::class, 'index'])->name('trang_chu');
//gói tập
Route::get('/goi-tap', [GoiTapController::class, 'index']);
// nhận giữ liệu đăng nhập từ form user gửi tới
Route::post('/dang-ky',[RegisterController::class,'store'])->name('register');
// nhan thong tin dang ky va luu
Route::get('/dang_nhap',[LoginController::class,'ShowLogin'])->name('login');
Route::post('/dang_nhap',[LoginController::class,'login'])->name('login.post');
//dang xuat tai khoan
Route::post('/logout',[LoginController::class,'logout'])->name('logout');

//ve trang chu admin
// Route dành cho trang quản trị
Route::get('/admin/dashboard', function () {
    return view('admin_dashboard'); // Hoặc return view('admin.index');
    })->name('admin_dashboard')->middleware('CheckRole:admin');