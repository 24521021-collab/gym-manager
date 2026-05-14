<table class="table align-middle">
    <thead>
        <tr>
            <th>Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>SKU</th>
            <th>Giá</th>
            <th>Kho</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $pro)
        <tr>
            <td>
                <img src="{{ asset('images/products/' . ($pro->image ?? 'default-product.jpg')) }}" 
                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
            </td>
            <td class="fw-bold">{{ $pro->name }}</td>
            <td><span class="badge bg-light text-dark border">{{ $pro->sku }}</span></td>
            <td class="text-primary">{{ number_format($pro->price) }}đ</td>
            <td>
                @if($pro->stock_quantity > 0)
                    <span class="badge bg-success-subtle text-success">Còn {{ $pro->stock_quantity }} món</span>
                @else
                    <span class="badge bg-danger-subtle text-danger">Hết hàng</span>
                @endif
            </td>
            <td>
                <button class="btn btn-sm btn-outline-warning">Sửa</button>
                <button class="btn btn-sm btn-outline-danger">Xóa</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>