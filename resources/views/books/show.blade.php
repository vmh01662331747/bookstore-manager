@extends('layouts.app')

@section('title', $book->title . ' - Chi tiết Sách')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <a href="{{ route('books.index') }}" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Danh sách Kho sách
        </a>
        <h2 class="fw-bold mb-0 mt-1">{{ $book->title }}</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('orders.create', ['book_id' => $book->id]) }}" class="btn btn-primary rounded-3 fw-semibold">
            <i class="bi bi-cart-plus me-1"></i> Bán cuốn sách này (POS)
        </a>
        <a href="{{ route('books.edit', $book) }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
        </a>
        <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Xác nhận xóa đầu sách này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger rounded-3"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="bv-card p-4 mb-4">
            <div class="d-flex flex-column flex-md-row gap-4 align-items-start">
                <div class="bv-book-cover bv-book-cover-lg" style="background: {{ $book->cover_color }};">
                    <div>
                        <div class="text-uppercase small opacity-75 mb-1">{{ $book->category?->name }}</div>
                        <div class="fw-bold">{{ $book->title }}</div>
                    </div>
                    <div>
                        <div class="small opacity-75">{{ $book->author?->name }}</div>
                        <div class="small mt-1">★ {{ $book->rating }} / 5.0</div>
                    </div>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-{{ $book->category?->color ?? 'primary' }}-subtle text-{{ $book->category?->color ?? 'primary' }} rounded-pill px-3 py-1">
                            {{ $book->category?->name }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 font-monospace">
                            ISBN: {{ $book->isbn }}
                        </span>
                    </div>

                    <div class="fs-3 fw-bold text-primary mb-2">{{ number_format($book->price, 0, ',', '.') }} đ</div>
                    <p class="text-secondary mb-3">{{ $book->description ?: 'Chưa có mô tả chi tiết cho đầu sách này.' }}</p>

                    <div class="row g-3 small">
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary">Tác giả</div>
                            <div class="fw-semibold">{{ $book->author?->name }}</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary">Nhà xuất bản</div>
                            <div class="fw-semibold">{{ $book->publisher ?: 'Đang cập nhật' }} ({{ $book->published_year }})</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary">Số trang</div>
                            <div class="fw-semibold">{{ $book->pages ?: '--' }} trang</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary">Giá vốn nhập</div>
                            <div class="fw-semibold">{{ number_format($book->cost_price, 0, ',', '.') }} đ</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary">Biên lợi nhuận gộp</div>
                            <div class="fw-semibold text-success">{{ $book->profit_margin }}%</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary">Tổng số lượng đã bán</div>
                            <div class="fw-semibold text-primary">{{ $book->sold_count }} cuốn</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử giao dịch của cuốn sách -->
        <div class="bv-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-receipt me-1 text-primary"></i> Lịch sử xuất bán của đầu sách</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                            <th>Ngày bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($book->orderItems as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $item->order_id) }}" class="fw-bold text-decoration-none">
                                        {{ $item->order?->order_code }}
                                    </a>
                                </td>
                                <td>{{ $item->order?->customer?->name ?? 'Khách lẻ' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $item->quantity }} cuốn</span></td>
                                <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                <td class="fw-bold">{{ number_format($item->line_total, 0, ',', '.') }} đ</td>
                                <td class="small text-secondary">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Chưa có giao dịch bán hàng nào cho đầu sách này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="bv-card p-4 mb-4">
            <h5 class="fw-bold mb-2"><i class="bi bi-box-seam text-primary me-1"></i> Quản lý Tồn kho Nhanh</h5>
            <p class="small text-secondary">Hiện có <strong class="fs-5 text-body">{{ $book->stock }}</strong> cuốn trong kho (Tối thiểu: {{ $book->min_stock }}).</p>

            @if ($book->recommended_restock > 0)
                <div class="alert alert-warning border-0 rounded-3 small">
                    <i class="bi bi-robot me-1"></i> <strong>Trợ lý AI gợi ý:</strong> Nên nhập bổ sung ít nhất <strong>{{ $book->recommended_restock }} cuốn</strong> để đáp ứng nhu cầu mua.
                </div>
            @endif

            <form action="{{ route('books.restock', $book) }}" method="POST">
                @csrf
                <label class="form-label small fw-semibold">Số lượng nhập thêm</label>
                <div class="input-group mb-3">
                    <input type="number" name="added_quantity" class="form-control" value="{{ max(10, $book->recommended_restock) }}" min="1" max="1000" required>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Nhập kho
                    </button>
                </div>
            </form>
        </div>

        <div class="bv-card p-4">
            <h6 class="fw-bold mb-3">Sách cùng thể loại ({{ $book->category?->name }})</h6>
            <div class="d-flex flex-column gap-3">
                @forelse ($relatedBooks as $rel)
                    <a href="{{ route('books.show', $rel) }}" class="d-flex align-items-center gap-3 text-decoration-none text-body">
                        <div class="bv-book-cover" style="background: {{ $rel->cover_color }};">
                            <span>{{ Str::limit($rel->title, 15) }}</span>
                        </div>
                        <div>
                            <div class="fw-semibold small">{{ $rel->title }}</div>
                            <div class="text-primary fw-bold small">{{ number_format($rel->price, 0, ',', '.') }} đ</div>
                        </div>
                    </a>
                @empty
                    <div class="small text-secondary">Không có sách cùng thể loại khác.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
