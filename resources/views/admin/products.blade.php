@extends('layout.admin_layout')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Quản lý kho hàng</h4>
        <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#modalProduct" onclick="resetForm()">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </button>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Tìm tên hoặc SKU..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-dark">
                    <i class="fas fa-search">tìm kiếm </i>
        </div>
            </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>SKU</th>
                    <th>Giá</th>
                    <th>Kho</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
                <tbody>
                @foreach($products as $pro)
                <tr>
                    <td class="ps-4">
                        <img src="{{ asset('images/products/' . ($pro->image ??'default-product.jpg')) }}" width="50" height="50" class="rounded shadow-sm" style="object-fit: cover;">
                    </td>
                    <td class="fw-bold">{{ $pro->name }}</td>
                    <td><code>{{ $pro->sku }}</code></td>
                    <td class="text-primary fw-bold">{{ number_format($pro->price) }}đ</td>
                    <td>
                        <span class="badge {{ $pro->stock_quantity > 5 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            Còn {{ $pro->stock_quantity }} cái
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-warning"
                            onclick='editProduct(@json($pro))'
                            data-bs-toggle="modal" data-bs-target="#modalProduct">
                            Sửa
                        </button>
                        <form action="{{ route('admin.products.destroy', $pro->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa thật không Long?')">Xóa</button>
                        </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
     <div class="card-footer clearfix">
            <div class="float-end">
            {{ $products->appends(request()->query())->links() }}
            </div>
    </div>
<div class="modal fade" id="modalProduct" tabindex="-1">
    <div class="modal-dialog modal-lg shadow-lg">
        <form id="productForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            <div class="modal-content border-0">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle">Thêm sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Tên sản phẩm</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">SKU</label>
                        <input type="text" name="sku" id="edit_sku" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Giá (VNĐ)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số lượng</label>
                        <input type="number" name="stock_quantity" id="edit_stock" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Hình ảnh</label>
                        <input type="file" name="image" id="edit_image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-dark px-5">Lưu lại</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    // Khi bấm nút "Sửa"
    function editProduct(data) {
        // 1. Đổi tiêu đề Modal để Admin biết đang sửa món nào
        document.getElementById('modalTitle').innerText = "Chỉnh sửa: " + data.name;

        // 2. Thay đổi Action của Form để trỏ đúng tới ID cần sửa
        // URL sẽ từ /admin/products thành /admin/products/5
        let url = "{{ route('admin.products.update', ':id') }}";
        document.getElementById('productForm').action = url.replace(':id', data.id);

        // 3. Đổ dữ liệu vào các ô Input
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_sku').value = data.sku;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_stock').value = data.stock_quantity;
        // Không gán giá trị cho input file vì lý do bảo mật trình duyệt

        // 4. Quan trọng: Thêm method PUT vì HTML Form không hỗ trợ PUT trực tiếp
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    }

    // Khi bấm nút "Thêm mới" (hoặc đóng modal)
    function resetForm() {
        document.getElementById('modalTitle').innerText = "Thêm sản phẩm mới";
        document.getElementById('productForm').action = "{{ route('admin.products.store') }}";
        document.getElementById('methodField').innerHTML = ''; // Xóa cái PUT đi để về POST gốc
        document.getElementById('productForm').reset(); // Xóa sạch chữ trong các ô input
    }
</script>
@endsection