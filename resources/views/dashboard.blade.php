@extends('layouts.app')

@section('title', 'Tổng quan Thông minh - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <span class="badge rounded-pill text-bg-primary bg-opacity-10 text-primary px-3 py-1 mb-2 fw-semibold">
            <i class="bi bi-cpu-fill me-1"></i> Smart Analytics & AI Inventory
        </span>
        <h2 class="fw-bold mb-0">Trung tâm Điều hành Nhà sách Thông minh</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('books.create') }}" class="btn btn-outline-primary rounded-3 fw-semibold">
            <i class="bi bi-journal-plus me-1"></i> Nhập sách mới
        </a>
        <a href="{{ route('orders.create') }}" class="btn btn-primary rounded-3 fw-semibold shadow-sm">
            <i class="bi bi-lightning-charge-fill me-1"></i> Bán hàng tại quầy (POS)
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bv-card bv-card-hover p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Tổng Doanh Thu Thực Tế</div>
                    <div class="fs-3 fw-bold text-primary">{{ number_format($totalRevenue, 0, ',', '.') }} đ</div>
                    <div class="small text-success mt-1">
                        <i class="bi bi-graph-up-arrow me-1"></i>Hôm nay: <strong>{{ number_format($todayRevenue, 0, ',', '.') }} đ</strong>
                    </div>
                </div>
                <span class="bv-kpi-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-cash-coin"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bv-card bv-card-hover p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Lợi Nhuận Gộp Ước Tính</div>
                    <div class="fs-3 fw-bold text-success">{{ number_format($estimatedProfit, 0, ',', '.') }} đ</div>
                    <div class="small text-secondary mt-1">
                        <i class="bi bi-receipt me-1"></i>Từ <strong>{{ $totalOrders }}</strong> đơn hàng phát sinh
                    </div>
                </div>
                <span class="bv-kpi-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-piggy-bank-fill"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bv-card bv-card-hover p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Kho Sách & Đầu Sách</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalStock) }} <span class="fs-6 fw-normal text-secondary">cuốn</span></div>
                    <div class="small text-secondary mt-1">
                        <i class="bi bi-journal-bookmark me-1"></i>Đang quản lý <strong>{{ $totalBooks }}</strong> đầu sách
                    </div>
                </div>
                <span class="bv-kpi-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-bookshelf"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bv-card bv-card-hover p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Cảnh Báo & Hội Viên</div>
                    <div class="fs-3 fw-bold {{ $lowStockBooks->count() > 0 ? 'text-danger' : 'text-body' }}">
                        {{ $lowStockBooks->count() }} <span class="fs-6 fw-normal text-secondary">sách sắp hết</span>
                    </div>
                    <div class="small text-secondary mt-1">
                        <i class="bi bi-award-fill text-warning me-1"></i><strong>{{ $totalCustomers }}</strong> hội viên tích điểm
                    </div>
                </div>
                <span class="bv-kpi-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-bell-fill"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Biểu đồ Doanh thu 7 ngày & Trợ lý AI Gợi ý Nhập kho -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-7">
        <div class="bv-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Biểu đồ Doanh thu 7 ngày gần nhất</h5>
                    <small class="text-secondary">Theo dõi xu hướng doanh số tại quầy POS & kênh Online</small>
                </div>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Real-time Data</span>
            </div>
            <div style="height: 290px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="bv-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-robot text-primary me-1"></i> Gợi ý Nhập kho Thông minh
                    </h5>
                    <small class="text-secondary">Tự động tính toán dựa trên tốc độ bán & tồn kho tối thiểu</small>
                </div>
                <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $lowStockBooks->count() }} cần nhập</span>
            </div>

            @if ($lowStockBooks->isEmpty())
                <div class="text-center py-5 text-secondary">
                    <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
                    Tất cả đầu sách đều đạt mức tồn kho an toàn!
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach ($lowStockBooks->take(4) as $book)
                        <div class="p-3 rounded-4 border d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-color: var(--bv-border) !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bv-book-cover" style="background: {{ $book->cover_color }};">
                                    <span>{{ Str::limit($book->title, 18) }}</span>
                                    <small style="font-size:0.55rem;opacity:0.85;">{{ $book->stock }} cuốn</small>
                                </div>
                                <div>
                                    <a href="{{ route('books.show', $book) }}" class="fw-bold text-decoration-none text-body d-block">
                                        {{ Str::limit($book->title, 34) }}
                                    </a>
                                    <div class="small text-secondary">
                                        Tồn: <strong class="text-danger">{{ $book->stock }}</strong> / Tối thiểu: {{ $book->min_stock }} &bull; Đã bán: <strong>{{ $book->sold_count }}</strong>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary mt-1">
                                        <i class="bi bi-magic me-1"></i>Đề xuất nhập: +{{ $book->recommended_restock }} cuốn
                                    </span>
                                </div>
                            </div>
                            <form action="{{ route('books.restock', $book) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="added_quantity" value="{{ $book->recommended_restock }}">
                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-3 fw-semibold">
                                    <i class="bi bi-box-arrow-in-down me-1"></i> Nhập ngay
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Top Sách Bán Chạy & Đơn Hàng Gần Đây -->
<div class="row g-4">
    <div class="col-12 col-xl-5">
        <div class="bv-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-fire text-danger me-1"></i> Top Sách Bán Chạy Nhất</h5>
                <a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="btn btn-sm btn-link text-decoration-none">Xem tất cả</a>
            </div>
            <div class="d-flex flex-column gap-3">
                @foreach ($topBooks as $idx => $book)
                    <div class="d-flex align-items-center gap-3">
                        <div class="fw-bold fs-5 text-secondary" style="width: 24px;">#{{ $idx + 1 }}</div>
                        <div class="bv-book-cover" style="background: {{ $book->cover_color }};">
                            <span>{{ Str::limit($book->title, 16) }}</span>
                            <small style="font-size:0.55rem;">★ {{ $book->rating }}</small>
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ route('books.show', $book) }}" class="fw-semibold text-decoration-none text-body d-block">
                                {{ Str::limit($book->title, 38) }}
                            </a>
                            <small class="text-secondary">{{ $book->author?->name }} &bull; {{ number_format($book->price, 0, ',', '.') }} đ</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                                {{ $book->sold_count }} đã bán
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="bv-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-1"></i> Hóa đơn Giao dịch Gần đây</h5>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-link text-decoration-none">Tất cả đơn hàng</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Kênh</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-primary">{{ $order->order_code }}</td>
                                <td>
                                    @if ($order->customer)
                                        <div class="fw-semibold">{{ $order->customer->name }}</div>
                                        <span class="badge bg-{{ $order->customer->tier_badge }}-subtle text-{{ $order->customer->tier_badge }}" style="font-size:0.68rem;">
                                            VIP {{ $order->customer->tier_label }}
                                        </span>
                                    @else
                                        <span class="text-secondary">Khách vãng lai</span>
                                    @endif
                                </td>
                                <td><span class="small text-secondary">{{ $order->channel_label }}</span></td>
                                <td class="fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge }}-subtle text-{{ $order->status_badge }} rounded-pill">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Chưa có giao dịch nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const labels = @json($chartLabels);
        const revenues = @json($chartRevenues);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenues,
                    backgroundColor: 'rgba(79, 70, 229, 0.78)',
                    borderColor: '#4f46e5',
                    borderWidth: 1,
                    borderRadius: 10,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (item) => new Intl.NumberFormat('vi-VN').format(item.raw) + ' đ'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (val) => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(val) + ' đ'
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
