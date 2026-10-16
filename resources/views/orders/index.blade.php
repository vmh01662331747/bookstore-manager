@extends('layouts.app')

@section('title', 'Quản lý Hóa đơn & Đơn hàng - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Danh sách Hóa đơn & Đơn hàng</h2>
        <p class="text-secondary small mb-0">Theo dõi toàn bộ giao dịch tại quầy POS và kênh trực tuyến, chiết khấu VIP và trạng thái thanh toán.</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary rounded-3 fw-semibold">
        <i class="bi bi-lightning-charge-fill me-1"></i> Tạo hóa đơn bán sách (POS)
    </a>
</div>

<div class="bv-card p-3 mb-4">
    <form method="GET" action="{{ route('orders.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo mã đơn (VD: BK-...) hoặc tên, SĐT khách hàng...">
        </div>
        <div class="col-6 col-md-3">
            <select name="channel" class="form-select">
                <option value="">-- Tất cả kênh bán --</option>
                <option value="pos" @selected(request('channel') === 'pos')>Tại quầy (POS)</option>
                <option value="online" @selected(request('channel') === 'online')>Trực tuyến (Online)</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                <option value="completed" @selected(request('status') === 'completed')>Hoàn tất</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-primary rounded-3">Lọc đơn hàng</button>
        </div>
    </form>
</div>

<div class="bv-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã hóa đơn</th>
                    <th>Khách hàng</th>
                    <th>Kênh & Thanh toán</th>
                    <th>Tạm tính</th>
                    <th>Ưu đãi VIP</th>
                    <th>Tổng thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th class="text-end">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="fw-bold text-primary font-monospace">{{ $order->order_code }}</td>
                        <td>
                            @if ($order->customer)
                                <a href="{{ route('customers.show', $order->customer) }}" class="fw-semibold text-decoration-none text-body">{{ $order->customer->name }}</a>
                                <div>
                                    <span class="badge bg-{{ $order->customer->tier_badge }}-subtle text-{{ $order->customer->tier_badge }}" style="font-size:0.68rem;">
                                        VIP {{ $order->customer->tier_label }}
                                    </span>
                                </div>
                            @else
                                <span class="text-secondary">Khách vãng lai</span>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $order->channel_label }}</div>
                            <div class="small text-secondary">{{ $order->payment_label }}</div>
                        </td>
                        <td>{{ number_format($order->subtotal, 0, ',', '.') }} đ</td>
                        <td class="text-danger">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</td>
                        <td class="fw-bold text-success">{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                        <td>
                            <span class="badge bg-{{ $order->status_badge }}-subtle text-{{ $order->status_badge }} rounded-pill px-3">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="small text-secondary">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                <i class="bi bi-eye"></i> Xem & In
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-secondary py-4">Không tìm thấy hóa đơn nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
