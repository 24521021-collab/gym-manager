<!DOCTYPE html>
<html>
<head>
    <title>Danh sách gói tập</title>
</head>
<body>
    <h1>Các gói tập hiện có tại Gym</h1>
    
    <table border="1">
        <thead>
            <tr>
                <th>id</th>
                <th>Tên gói</th>
                <th>Thời hạn (ngày)</th>
                <th>Giá tiền</th>
                <th>Mô tả</th>
            </tr>
        </thead>
        <tbody>
            @foreach($goiTaps as $item)
            <tr>
                <td>GT{{ sprintf('%02d', $item->id) }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->duration_days }}</td>
                <td>{{ number_format($item->price) }} VNĐ</td>
                <td>{{$item->description }}
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>