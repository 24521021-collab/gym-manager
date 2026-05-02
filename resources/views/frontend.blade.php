<!DOCTYPE html>
<html>
<head>
    <title>Danh sách gói tập</title>
</head>
<body>
    <h1>Các gói tập hiện có tại phòng Gym</h1>
    
    <table border="1">
        <tr>
            <th>Tên gói</th>
            <th>Giá</th>
            <th>Mô tả</th>
        </tr>
        @foreach($danhSachGoi as $goi)
        <tr>
            <td>{{ $goi->ten_goi }}</td>
            <td>{{ number_format($goi->gia) }} VNĐ</td>
            <td>{{ $goi->mo_ta }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>