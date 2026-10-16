@extends('layouts.app')

@section('title', $customer->name . ' - Hồ sơ Hội viên VIP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('customers.index') }}" class="text-decoration-none small text-secondary"><i class="bi bi-arrow-left me-1"></i> Danh sách Hội viên</a>
        <h2 class="fw-bold mb-0 mt-1">{{ $customer->name }}</h2>
    </div>
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary rounded-3">
        <i class="bi bi-pencil me-1"></i> Sửa thông tin
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="bv-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-{{ $customer->tier_badge }}-subtle text-{{ $customer->tier_badge }} rounded-pill px-3 py-2 fs-6">
                    <i class="bi bi-gem me-1"></i> Hạng {{ $customer->tier_label }}
                </span>
                <span class="fw-bold text-primary">{{ $customer->points }} điểm</span>
            </div>
            <div class="mb-2 small text-secondary">Số điện thoại: <strong class="text-body">{{ $customer->phone }}</strong></div>
            <div class="mb-2 small text-secondary">Email: <strong class="text-body">{{ $customer->email ?: '--' }}</strong></div>
            <div class="mb-3 small text-secondary">Địa chỉ: <strong class="text-body">{{ $customer->address ?: '--' }}</strong></div>
            <div class="p-3 rounded-3 bg-primary bg-opacity-10">
                <div class="small text-secondary">Tổng chi tiêu tích lũy</div>
                <div class="fs-4 fw-bold text-primary">{{ number_format($customer->total_spent, 0, ',', '.') }} đ</div>
                <div class="small text-success mt-1">Ưu đãi chiết khấu tự động: <strong>-{{ $customer->discount_percent }}%</strong> mọi hóa đơn</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="bv-card p-4">
            <h5 class="fw-bold mb-3">Lịch sử Mua sách của Hội viên</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Số đầu sách</th>
                            <th>Giảm giá VIP</th>
                            <th>Thành tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày mua</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->orders as $o)
                            <tr>
                                <td><a href="{{ route('orders.show', $o) }}" class="fw-bold text-decoration-none">{{ $o->order_code }}</a></td>
                                <td>{{ $o->items->sum('quantity') }} cuốn</td>
                                <td class="text-danger">-{{ number_format($o->discount_amount, 0, ',', '.') }} đ</td>
                                <td class="fw-bold text-success">{{ number_format($o->total_amount, 0, ',', '.') }} đ</td>
                                <td><span class="badge bg-{{ $o->status_badge }}-subtle text-{{ $o->status_badge }}">{{ $o->status_label }}</span></td>
                                <td class="small text-secondary">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
