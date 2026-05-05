
<div class="container py-5">
    <h3 class="mb-4">Gói tập của tôi</h3>
    
    @if($memberships->isEmpty())
        <div class="alert alert-info">Bạn chưa đăng ký gói tập nào. <a href="/">Đăng ký ngay!</a></div>
    @else
        <div class="row">
            @foreach($memberships as $ms)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-{{ $ms->status == 'Active' ? 'success' : 'secondary' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title text-primary">{{ $ms->package->package_name }}</h5>
                            <span class="badge {{ $ms->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $ms->status }}
                            </span>
                        </div>
                        <hr>
                        <p><strong>Ngày bắt đầu:</strong> {{ \Carbon\Carbon::parse($ms->start_date)->format('d/m/Y') }}</p>
                        <p><strong>Ngày hết hạn:</strong> {{ \Carbon\Carbon::parse($ms->end_date)->format('d/m/Y') }}</p>
                        <p class="text-muted small">ID Đơn hàng: #{{ $ms->id }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>