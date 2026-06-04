# KOR GYM MANAGEMENT SYSTEM

## Giới thiệu

KOR GYM MANAGEMENT SYSTEM là hệ thống quản lý phòng tập Gym được phát triển trong khuôn khổ môn học **IS207 - Phát triển ứng dụng Web** tại Trường Đại học Công nghệ Thông tin - ĐHQG TP.HCM.

Hệ thống hỗ trợ quản lý hội viên, gói tập, lớp học, huấn luyện viên cá nhân (PT), bán sản phẩm thể thao và theo dõi sức khỏe người dùng trên cùng một nền tảng web.

---

## Thông tin môn học

| Thông tin  | Nội dung                                  |
| ---------- | ----------------------------------------- |
| Môn học    | IS207 - Phát triển ứng dụng Web           |
| Giảng viên | ThS. Trình Trọng Tín                      |
| Năm học    | 2025 - 2026                               |
| Trường     | Đại học Công nghệ Thông tin - ĐHQG TP.HCM |

---

## Thành viên nhóm

| MSSV     | Họ và tên           | Vai trò                             |
| -------- | ------------------- | ----------------------------------- |
| 24520942 | Xa Văn Lâm          | Nhóm trưởng, Dashboard, Integration |
| 24521021 | Trịnh Duy Long      | Shop, Cart, Checkout                |
| 24521838 | Nguyễn Thanh Trí    | UI/UX, Booking, Notification        |
| 24520275 | Ngô Trọng Đạt       | PT Dashboard, Check-in, BMI         |
| 24521027 | Lê Lưu Luân         | Membership, Class Management        |
| 24520606 | Nguyễn Hữu Phú Hưng | Authentication, Profile             |

---

# Mô tả đề tài

Dự án xây dựng hệ thống quản lý phòng Gym theo mô hình B2C, cho phép khách hàng đăng ký gói tập, tham gia lớp học, đặt lịch huấn luyện viên cá nhân, mua sản phẩm thể thao và theo dõi quá trình luyện tập.

Ngoài các chức năng dành cho hội viên, hệ thống còn cung cấp khu vực quản trị dành cho Admin và PT nhằm hỗ trợ vận hành phòng gym một cách hiệu quả.

---

# Công nghệ sử dụng

## Backend

* PHP 8.x
* Laravel 12
* Eloquent ORM
* Laravel Socialite

## Frontend

* HTML5
* CSS3
* JavaScript
* Blade Template
* TailwindCSS
* Bootstrap

## Database

* MySQL

## Công cụ phát triển

* Git & GitHub
* Visual Studio Code
* Figma
* Postman

---

# Chức năng hệ thống

## Người dùng

### Xác thực

* Đăng ký tài khoản
* Đăng nhập bằng Email/Số điện thoại
* Đăng nhập Google
* Quên mật khẩu
* Đổi mật khẩu

### Quản lý hội viên

* Đăng ký gói tập
* Theo dõi thời hạn gói tập
* Quản lý hồ sơ cá nhân

### Đặt lớp học

* Xem danh sách lớp học
* Đăng ký lớp học
* Hủy đăng ký

### Đặt lịch PT

* Xem danh sách PT
* Đặt lịch tập cá nhân
* Theo dõi trạng thái lịch hẹn

### Cửa hàng thể thao

* Xem sản phẩm
* Tìm kiếm sản phẩm
* Thêm vào giỏ hàng
* Thanh toán
* Xem lịch sử đơn hàng

### Theo dõi sức khỏe

* Cập nhật cân nặng
* Cập nhật chiều cao
* Tính BMI
* Lưu lịch sử chỉ số cơ thể

### Thông báo và đánh giá

* Nhận thông báo hệ thống
* Đánh giá PT
* Đánh giá lớp học

---

## Quản trị viên (Admin)

### Dashboard

* Thống kê doanh thu
* Thống kê hội viên
* Thống kê đơn hàng
* Thống kê lớp học

### Quản lý dữ liệu

* Quản lý gói tập
* Quản lý lớp học
* Quản lý hội viên
* Quản lý sản phẩm
* Quản lý đơn hàng
* Quản lý PT Booking
* Quản lý bài viết

### Vận hành

* Check-in hội viên
* Theo dõi hoạt động hệ thống

---

## Huấn luyện viên (PT)

* Quản lý lớp học phụ trách
* Xem lịch PT Booking
* Ghi nhật ký huấn luyện
* Theo dõi hoạt động cá nhân

---

# Kiến trúc hệ thống

Hệ thống được phát triển theo mô hình MVC (Model - View - Controller).

* Model: Quản lý dữ liệu và quan hệ Eloquent.
* View: Blade Template.
* Controller: Xử lý nghiệp vụ.
* Route: Điều hướng yêu cầu người dùng.
* Middleware: Kiểm tra xác thực và phân quyền.


---
# Cài đặt hệ thống

---
## Yêu cầu hệ thống

Trước khi cài đặt, cần đảm bảo môi trường phát triển đáp ứng các yêu cầu sau:

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 20.x
- NPM >= 10.x
- MySQL >= 8.0
- Git
---

## Clone source code

```bash
git clone <repository-url>
cd gym-manager
```

## Cài đặt thư viện

```bash
composer install
npm install
```

## Cấu hình môi trường

```bash
cp .env.example .env
```

Cấu hình cơ sở dữ liệu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_manager
DB_USERNAME=root
DB_PASSWORD=
```

## Khởi tạo hệ thống

```bash
php artisan key:generate

php artisan migrate --seed
```

## Build frontend

```bash
npm run build
```

## Chạy hệ thống

```bash
php artisan serve
```

Truy cập:

http://127.0.0.1:8000

---

# Cấu trúc dự án

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Auth/
│   │   ├── Pt/
│   │   └── ...
│   └── Middleware/

app/Models/

resources/views/
├── admin/
├── customer/
├── pt/
├── layout/

database/
├── migrations/
└── seeders/

routes/
└── web.php
```

---

# Giấy phép

Dự án được phát triển phục vụ mục đích học tập trong môn IS207 - Phát triển ứng dụng Web.
