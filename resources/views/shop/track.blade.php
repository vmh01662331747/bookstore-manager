@extends('shop.layout')

@section('title', 'Tra Cứu Đơn Hàng & Vận Đơn - BookVerse.vn')

@section('content')
<div class="shop-card p-4 mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-5">
            <h4 class="font-serif-book fw-bold mb-1">
                <i class="bi bi-box-seam text-danger me-1"></i> Tra cứu Vận đơn &amp; Lịch sử Mua sách
            </h4>
            <p class="text-secondary small mb-0">
                Nhập mã đơn hàng (VD: <code>BK-261016-01</code>) hoặc số điện thoại hội viên (VD: <code>0901234567</code>) để xem chi tiết.
            </p>
        </div>
        <div class="col-12 col-lg-7">
            <form method="GET" action="{{ route('shop.track') }}" class="row g-2">
                <div class="col-12 col-md-5">
                    <input type="text" name="code" value="{{ $code }}" class="form-control" placeholder="Mã đơn (VD: BK-261016-01)">
                </div>
                <div class="col-12 col-md-5">
                    <input type="text" name="phone" value="{{ $phone }}" class="form-control" placeholder="Số điện thoại (VD: 0901234567)">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-bv-primary rounded-2">Tra cứu</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($order)
    <div class="shop-card p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 mb-1">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ $order->status_label }}
                </span>
                <h4 class="fw-bold font-monospace mb-0">Mã vận đơn: {{ $order->order_code }}</h4>
                <small class="text-secondary">
                    Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }} &bull; Kênh: {{ $order->channel_label }}
                </small>
            </div>
            <div class="text-md-end mt-2 mt-md-0">
                <div class="small text-secondary">Tổng thanh toán</div>
                <div class="fs-3 fw-bold text-danger">{{ number_format($order->total_amount, 0, ',', '.') }} đ</div>
            </div>
        </div>

        <!-- Tiến trình xử lý đơn hàng -->
        <div class="row text-center g-2 mb-4 p-3 rounded-3" style="background: #faf8f5; border: 1px solid #e5e0d5;">
            <div class="col-4">
                <i class="bi bi-journal-check fs-4 text-success d-block"></i>
                <strong class="small d-block">1. Tiếp nhận đơn sách</strong>
                <small class="text-secondary" style="font-size:0.72rem;">Đã xác nhận tồn kho</small>
            </div>
            <div class="col-4">
                <i class="bi bi-box-seam-fill fs-4 text-success d-block"></i>
                <strong class="small d-block">2. Bọc Plastic &amp; Đóng gói</strong>
                <small class="text-secondary" style="font-size:0.72rem;">Hộp chống sốc 3 lớp</small>
            </div>
            <div class="col-4">
                <i class="bi bi-patch-check-fill fs-4 {{ $order->status === 'cancelled' ? 'text-danger' : 'text-success' }} d-block"></i>
                <strong class="small d-block">3. {{ $order->status_label }}</strong>
                <small class="text-secondary" style="font-size:0.72rem;">Cộng điểm tích lũy VIP</small>
            </div>
        </div>

        <div class="row g-3 mb-3 small">
            <div class="col-12 col-md-6">
                <strong>Người nhận:</strong> {{ $order->customer?->name ?? 'Khách vãng lai' }} ({{ $order->customer?->phone }})
                @if ($order->customer)
                    <span class="badge bg-danger-subtle text-danger ms-1">Hạng {{ $order->customer->tier_label }}</span>
                @endif
            </div>
            <div class="col-12 col-md-6">
                <strong>Thanh toán &amp; Ghi chú:</strong> {{ $order->payment_label }} — {{ $order->note }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Tên ấn phẩm</th>
                        <th class="text-center" style="width: 110px;">Số lượng</th>
                        <th class="text-end" style="width: 140px;">Đơn giá</th>
                        <th class="text-end" style="width: 150px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->book?->title }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($item->line_total, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if ($customerOrders->isNotEmpty())
    <div class="shop-card p-4">
        <h5 class="font-serif-book fw-bold mb-3">Lịch sử đơn đặt sách của số điện thoại {{ $phone }}</h5>
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Kênh mua</th>
                        <th>Tổng thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customerOrders as $co)
                        <tr>
                            <td class="fw-bold font-monospace text-danger">{{ $co->order_code }}</td>
                            <td>{{ $co->channel_label }}</td>
                            <td class="fw-bold">{{ number_format($co->total_amount, 0, ',', '.') }} đ</td>
                            <td><span class="badge bg-{{ $co->status_badge }}-subtle text-{{ $co->status_badge }}">{{ $co->status_label }}</span></td>
                            <td class="text-secondary">{{ $co->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('shop.track', ['code' => $co->order_code, 'phone' => $phone]) }}" class="btn btn-sm btn-bv-outline rounded-2">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
