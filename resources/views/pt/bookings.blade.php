@extends('layout.frontend')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="fas fa-user-clock me-2 text-success"></i> Quản lý lịch đặt PT riêng</h2>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="text-start ps-3">Học viên</th>
                        <th>Ngày tập</th>
                        <th>Khung giờ</th>
                        <th>Giá buổi tập</th>
                        <th>Hoa hồng (80%)</th>
                        <th>Ghi chú</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Duyệt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="text-start ps-3 fw-bold">{{ optional($booking->customer)->full_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-light text-dark">{{ $booking->start_time }} - {{ $booking->end_time }}</span></td>
                        <td class="fw-bold text-primary">{{ number_format($booking->price) }}đ</td>
                        <td class="fw-bold text-success">{{ number_format($booking->price * 0.8) }}đ</td>
                        <td class="small text-muted">{{ $booking->note ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $booking->status == 'pending' ? 'bg-warning' : ($booking->status == 'confirmed' ? 'bg-success' : 'bg-secondary') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            @if($booking->status == 'pending')
                            <form action="{{ route('pt.bookings.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                            </form>
                            <form action="{{ route('pt.bookings.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                            </form>
                            @else
                                <span class="text-muted small">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-5">Chưa có khách hàng nào đặt lịch tập riêng.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
