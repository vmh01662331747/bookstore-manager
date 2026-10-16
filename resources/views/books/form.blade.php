@extends('layouts.app')

@section('title', ($book->exists ? 'Chỉnh sửa Sách' : 'Thêm Sách Mới') . ' - BookVerse AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $book->exists ? 'Chỉnh sửa thông tin sách' : 'Thêm đầu sách mới vào kho' }}</h2>
        <p class="text-secondary small mb-0">Cập nhật thông tin xuất bản, định giá, biên lợi nhuận và ngưỡng cảnh báo tồn kho tự động.</p>
    </div>
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary rounded-3">
        <i class="bi bi-arrow-left me-1"></i> Quay lại kho sách
    </a>
</div>

<div class="bv-card p-4">
    <form method="POST" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}">
        @csrf
        @if ($book->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Mã ISBN <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="isbn" id="isbnInput" class="form-control" value="{{ old('isbn', $book->isbn) }}" required placeholder="978-604-...">
                    <button type="button" class="btn btn-outline-secondary" id="genIsbnBtn" title="Sinh mã ISBN ngẫu nhiên">
                        <i class="bi bi-magic"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-8">
                <label class="form-label fw-semibold">Tên tác phẩm sách <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}" required placeholder="Nhập tên đầy đủ của cuốn sách">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Danh mục thể loại <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Chọn thể loại --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $book->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Tác giả <span class="text-danger">*</span></label>
                <select name="author_id" class="form-select" required>
                    <option value="">-- Chọn tác giả --</option>
                    @foreach ($authors as $au)
                        <option value="{{ $au->id }}" @selected(old('author_id', $book->author_id) == $au->id)>{{ $au->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Nhà xuất bản</label>
                <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}" placeholder="VD: NXB Trẻ, NXB Thế Giới">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Năm xuất bản</label>
                <input type="number" name="published_year" class="form-control" value="{{ old('published_year', $book->published_year) }}">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Số trang</label>
                <input type="number" name="pages" class="form-control" value="{{ old('pages', $book->pages) }}">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Giá vốn nhập (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" name="cost_price" id="costPriceInput" class="form-control" value="{{ old('cost_price', $book->cost_price ?? 120000) }}" required min="0">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Giá bán lẻ (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" name="price" id="sellPriceInput" class="form-control" value="{{ old('price', $book->price ?? 210000) }}" required min="1000">
                <small id="marginHint" class="text-success fw-semibold"></small>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Số lượng tồn kho <span class="text-danger">*</span></label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', $book->stock) }}" required min="0">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Ngưỡng cảnh báo hết hàng <span class="text-danger">*</span></label>
                <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $book->min_stock) }}" required min="1">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Điểm đánh giá (1-5 ★)</label>
                <input type="number" step="0.1" min="1" max="5" name="rating" class="form-control" value="{{ old('rating', $book->rating) }}">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Màu bìa sách 3D</label>
                <input type="color" name="cover_color" class="form-control form-control-color w-100" value="{{ old('cover_color', $book->cover_color ?? '#4f46e5') }}">
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Mô tả tóm tắt nội dung sách</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Giới thiệu ngắn gọn nội dung và giá trị nổi bật của cuốn sách...">{{ old('description', $book->description) }}</textarea>
            </div>

            <div class="col-12 d-flex gap-4 pt-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" @checked(old('is_featured', $book->is_featured))>
                    <label class="form-check-label fw-semibold" for="isFeatured">Đánh dấu Sách Nổi Bật (Bestseller / Đề xuất)</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $book->is_active ?? true))>
                    <label class="form-check-label fw-semibold" for="isActive">Đang mở bán tại POS & Online</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('books.index') }}" class="btn btn-light rounded-3 px-4">Hủy</a>
            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> {{ $book->exists ? 'Lưu cập nhật' : 'Thêm vào kho sách' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const costEl = document.getElementById('costPriceInput');
        const priceEl = document.getElementById('sellPriceInput');
        const hintEl = document.getElementById('marginHint');

        function updateMargin() {
            const cost = parseFloat(costEl?.value || 0);
            const price = parseFloat(priceEl?.value || 0);
            if (price > 0) {
                const profit = price - cost;
                const margin = ((profit / price) * 100).toFixed(1);
                hintEl.textContent = `Lãi gộp: ${new Intl.NumberFormat('vi-VN').format(profit)} đ (${margin}%)`;
                hintEl.className = profit >= 0 ? 'small text-success fw-semibold' : 'small text-danger fw-semibold';
            }
        }
        costEl?.addEventListener('input', updateMargin);
        priceEl?.addEventListener('input', updateMargin);
        updateMargin();

        document.getElementById('genIsbnBtn')?.addEventListener('click', () => {
            const rand = Math.floor(100000000 + Math.random() * 900000000);
            document.getElementById('isbnInput').value = '978-' + rand;
        });
    })();
</script>
@endpush
