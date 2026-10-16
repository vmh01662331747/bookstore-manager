@extends('layouts.app')

@section('title', 'Hóa đơn ' . $order->order_code . ' - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 no-print">
    <div>
        <a href="{{ route('orders.index') }}" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Danh sách Hóa đơn
        </a>
        <h2 class="fw-bold mb-0 mt-1">Chi tiết Hóa đơn {{ $order->order_code }}</h2>
    </div>
    <div class="d-flex gap-2">
        <button type="button" onclick="window.print()" class="btn btn-outline-primary rounded-3 fw-semibold">
            <i class="bi bi-printer-fill me-1"></i> In Hóa Đơn
        </button>
        @if ($order->status !== 'cancelled')
            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Xác nhận hủy đơn hàng này và hoàn trả sách về kho?');">
                @csrf
                <button type="submit" class="btn btn-outline-danger rounded-3 fw-semibold">
                    <i class="bi bi-x-circle me-1"></i> Hủy đơn & Hoàn kho
                </button>
            </form>
        @endif
    </div>
</div>

<div class="bv-card p-4 p-md-5 mx-auto" style="max-width: 860px;">
    <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-4 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-book-half fs-3 text-primary"></i>
                <h4 class="fw-bold mb-0">NHÀ SÁCH THÔNG MINH BOOKVERSE AI</h4>
            </div>
            <div class="small text-secondary">Hệ thống Quản lý Bán sách & Kho Tri Thức Hiện Đại</div>
            <div class="small text-secondary">Hotline: 1900 6868 &bull; Website: bookverse.vn</div>
        </div>
        <div class="text-md-end mt-3 mt-md-0">
            <span class="badge bg-{{ $order->status_badge }}-subtle text-{{ $order->status_badge }} rounded-pill px-3 py-2 mb-2">
                {{ $order->status_label }}
            </span>
            <div class="fw-bold font-monospace fs-5">{{ $order->order_code }}</div>
            <div class="small text-secondary">Ngày lập: {{ $order->created_at?->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-3 bg-body-tertiary h-100">
                <div class="text-uppercase small fw-bold text-secondary mb-1">Thông tin Khách hàng</div>
                @if ($order->customer)
                    <div class="fw-bold">{{ $order->customer->name }}</div>
                    <div class="small text-secondary">SĐT: {{ $order->customer->phone }}</div>
                    <div class="small text-secondary">Hạng thẻ: <strong class="text-primary">VIP {{ $order->customer->tier_label }}</strong></div>
                @else
                    <div class="fw-semibold">Khách mua lẻ tại quầy</div>
                    <div class="small text-secondary">Không áp dụng thẻ hội viên</div>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-3 bg-body-tertiary h-100">
                <div class="text-uppercase small fw-bold text-secondary mb-1">Thông tin Giao dịch</div>
                <div class="small">Kênh bán: <strong>{{ $order->channel_label }}</strong></div>
                <div class="small">Phương thức thanh toán: <strong>{{ $order->payment_label }}</strong></div>
                <div class="small">Ghi chú: <strong>{{ $order->note ?: 'Không có' }}</strong></div>
            </div>
        </div>
    </div>

    <div class="table-responsive mb-4">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tên sách & Mã ISBN</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->book?->title }}</div>
                            <small class="text-secondary font-monospace">ISBN: {{ $item->book?->isbn }}</small>
                        </td>
                        <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                        <td class="text-end fw-bold">{{ number_format($item->line_total, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row justify-content-end">
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between py-1">
                <span class="text-secondary">Tạm tính:</span>
                <span class="fw-semibold">{{ number_format($order->subtotal, 0, ',', '.') }} đ</span>
            </div>
            <div class="d-flex justify-content-between py-1 text-danger">
                <span>Chiết khấu hội viên VIP:</span>
                <span>-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-top mt-2 fs-5 fw-bold text-success">
                <span>Tổng cộng thanh toán:</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
            </div>
        </div>
    </div>

    <div class="text-center text-secondary small mt-4 pt-3 border-top">
        Cảm ơn Quý khách đã mua sách tại <strong>BookVerse AI</strong>! Chúc Quý khách có những giờ phút đọc sách tuyệt vời.
    </div>
</div>
@endsection
