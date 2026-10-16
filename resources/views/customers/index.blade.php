@extends('layouts.app')

@section('title', 'Khách hàng & Hội viên VIP - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Khách hàng & Hội viên Thông minh</h2>
        <p class="text-secondary small mb-0">
            Tự động tích điểm (10.000đ = 1 điểm) và nâng hạng chiết khấu:
            <span class="badge bg-dark-subtle text-body">Đồng (0%)</span>
            <span class="badge bg-secondary-subtle text-secondary">Bạc ≥ 500k (-3%)</span>
            <span class="badge bg-warning-subtle text-warning">Vàng ≥ 2tr (-5%)</span>
            <span class="badge bg-info-subtle text-info">Kim Cương ≥ 5tr (-10%)</span>
        </p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary rounded-3 fw-semibold">
        <i class="bi bi-person-plus-fill me-1"></i> Thêm hội viên mới
    </a>
</div>

<div class="bv-card p-3 mb-4">
    <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên, số điện thoại hoặc email...">
        </div>
        <div class="col-8 col-md-4">
            <select name="tier" class="form-select">
                <option value="">-- Tất cả hạng thẻ --</option>
                <option value="diamond" @selected(request('tier') === 'diamond')>Hạng Kim Cương (-10%)</option>
                <option value="gold" @selected(request('tier') === 'gold')>Hạng Vàng (-5%)</option>
                <option value="silver" @selected(request('tier') === 'silver')>Hạng Bạc (-3%)</option>
                <option value="bronze" @selected(request('tier') === 'bronze')>Hạng Đồng (0%)</option>
            </select>
        </div>
        <div class="col-4 col-md-2 d-grid">
            <button type="submit" class="btn btn-primary rounded-3">Lọc hội viên</button>
        </div>
    </form>
</div>

<div class="bv-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Khách hàng</th>
                    <th>Liên hệ</th>
                    <th>Hạng Hội Viên</th>
                    <th>Điểm tích lũy</th>
                    <th>Tổng chi tiêu</th>
                    <th>Số đơn</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $c)
                    <tr>
                        <td>
                            <a href="{{ route('customers.show', $c) }}" class="fw-bold text-decoration-none text-body">{{ $c->name }}</a>
                            <div class="small text-secondary">{{ $c->address ?: 'Chưa cập nhật địa chỉ' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold font-monospace">{{ $c->phone }}</div>
                            <div class="small text-secondary">{{ $c->email }}</div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $c->tier_badge }}-subtle text-{{ $c->tier_badge }} rounded-pill px-3 py-1">
                                <i class="bi bi-gem me-1"></i>{{ $c->tier_label }} (-{{ $c->discount_percent }}%)
                            </span>
                        </td>
                        <td><span class="fw-bold text-primary">{{ number_format($c->points) }} điểm</span></td>
                        <td class="fw-bold text-success">{{ number_format($c->total_spent, 0, ',', '.') }} đ</td>
                        <td>{{ $c->orders_count }} đơn</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customers.show', $c) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('customers.edit', $c) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Xóa khách hàng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">Chưa có khách hàng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
