@extends('layouts.app')

@section('title', 'Kho Sách & Quản trị Tồn kho - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Kho Sách & Quản lý Tồn kho Thông minh</h2>
        <p class="text-secondary mb-0 small">Tìm kiếm đa chiều theo tên sách, ISBN, tác giả, danh mục và cảnh báo tồn kho tự động.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('books.create') }}" class="btn btn-primary rounded-3 fw-semibold">
            <i class="bi bi-plus-circle-fill me-1"></i> Thêm đầu sách mới
        </a>
    </div>
</div>

<!-- Bộ lọc thông minh -->
<div class="bv-card p-3 mb-4">
    <form method="GET" action="{{ route('books.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold text-secondary mb-1">Từ khóa (Tên / ISBN / Tác giả)</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="VD: Clean Code, 978-...">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-secondary mb-1">Thể loại</label>
            <select name="category_id" class="form-select">
                <option value="">-- Tất cả --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-secondary mb-1">Tác giả</label>
            <select name="author_id" class="form-select">
                <option value="">-- Tất cả --</option>
                @foreach ($authors as $au)
                    <option value="{{ $au->id }}" @selected(request('author_id') == $au->id)>{{ $au->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-secondary mb-1">Trạng thái kho</label>
            <select name="stock_status" class="form-select">
                <option value="">-- Mọi trạng thái --</option>
                <option value="in" @selected(request('stock_status') === 'in')>Còn hàng dồi dào</option>
                <option value="low" @selected(request('stock_status') === 'low')>Sắp hết hàng</option>
                <option value="out" @selected(request('stock_status') === 'out')>Hết hàng (0)</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-secondary mb-1">Sắp xếp</label>
            <select name="sort" class="form-select">
                <option value="latest" @selected(request('sort') === 'latest')>Mới cập nhật</option>
                <option value="bestseller" @selected(request('sort') === 'bestseller')>Bán chạy nhất</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá thấp → cao</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá cao → thấp</option>
                <option value="stock_asc" @selected(request('sort') === 'stock_asc')>Tồn kho ít nhất</option>
            </select>
        </div>
        <div class="col-12 col-md-1 d-grid">
            <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-funnel-fill"></i></button>
        </div>
    </form>
</div>

<!-- Danh sách sách dạng Card Grid hiện đại -->
<div class="row g-4">
    @forelse ($books as $book)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="bv-card bv-card-hover p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('books.show', $book) }}" class="text-decoration-none">
                            <div class="bv-book-cover" style="width:72px;height:102px;background:{{ $book->cover_color }};font-size:0.7rem;">
                                <span>{{ Str::limit($book->title, 22) }}</span>
                                <small style="font-size:0.6rem;opacity:0.9;">★ {{ $book->rating }}</small>
                            </div>
                        </a>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap gap-1 mb-1">
                                <span class="badge bg-{{ $book->category?->color ?? 'primary' }}-subtle text-{{ $book->category?->color ?? 'primary' }} rounded-pill">
                                    {{ $book->category?->name }}
                                </span>
                                @if ($book->is_featured)
                                    <span class="badge bg-warning-subtle text-warning rounded-pill"><i class="bi bi-star-fill"></i> Nổi bật</span>
                                @endif
                            </div>
                            <h6 class="fw-bold mb-1">
                                <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-body">{{ $book->title }}</a>
                            </h6>
                            <div class="small text-secondary mb-1">
                                <i class="bi bi-feather me-1"></i>{{ $book->author?->name }} &bull; <span class="font-monospace">{{ $book->isbn }}</span>
                            </div>
                            <div class="fs-5 fw-bold text-primary">{{ number_format($book->price, 0, ',', '.') }} đ</div>
                        </div>
                    </div>

                    <p class="small text-secondary mt-3 mb-2">{{ Str::limit($book->description, 95) }}</p>
                </div>

                <div class="pt-3 border-top mt-2 d-flex justify-content-between align-items-center" style="border-color: var(--bv-border) !important;">
                    <div>
                        @if ($book->is_out_of_stock)
                            <span class="badge bg-danger rounded-pill">Hết hàng (0)</span>
                        @elseif ($book->is_low_stock)
                            <span class="badge bg-warning text-dark rounded-pill">Sắp hết: còn {{ $book->stock }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success rounded-pill">Tồn kho: {{ $book->stock }}</span>
                        @endif
                        <small class="text-secondary ms-1">Đã bán: {{ $book->sold_count }}</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('orders.create', ['book_id' => $book->id]) }}" class="btn btn-outline-primary" title="Bán ngay tại POS">
                            <i class="bi bi-cart-plus"></i>
                        </a>
                        <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary" title="Chi tiết & Nhập kho">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-outline-secondary" title="Sửa">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="bv-card p-5 text-center text-secondary">
                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                Không tìm thấy đầu sách nào khớp với bộ lọc hiện tại.
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $books->links() }}
</div>
@endsection
