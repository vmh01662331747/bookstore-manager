@extends('shop.layout')

@section('title', 'BookVerse.vn - Nhà Sách Trực Tuyến & Xuất Bản Phẩm Chính Hãng')

@section('content')
@php
    $spotlightBook = $featuredBooks->first();
    $currentCategory = $categories->firstWhere('id', (int) request('category_id'));
    $currentAuthor = $authors->firstWhere('id', (int) request('author_id'));
@endphp

<!-- Khu vực Hero & Bảng xếp hạng Sách Bán Chạy (Chỉ hiện đầy đủ khi ở trang chủ mặc định) -->
@if (!request('q') && !request('category_id') && !request('author_id'))
<div class="row g-3 mb-4">
    <!-- Banner Tiêu điểm Xuất bản phẩm (8 cột) -->
    <div class="col-12 col-lg-8">
        <div class="shop-card h-100 p-4 p-md-5 position-relative overflow-hidden"
             style="background: linear-gradient(135deg, #1c1917 0%, #450a0a 60%, #7f1d1d 100%); color: #fff;">
            <div class="row align-items-center g-4">
                <div class="col-12 col-md-7">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3"
                         style="background: rgba(251, 191, 36, 0.18); border: 1px solid rgba(251, 191, 36, 0.45); color: #fde68a; font-size: 0.76rem; font-weight: 700;">
                        <i class="bi bi-award-fill"></i> ẤN PHẨM TIÊU ĐIỂM TRONG THÁNG
                    </div>

                    @if ($spotlightBook)
                        <h1 class="font-serif-book fw-bold mb-2" style="font-size: 1.9rem; line-height: 1.25;">
                            {{ $spotlightBook->title }}
                        </h1>
                        <div class="small mb-3" style="color: #fca5a5;">
                            Tác giả: <strong class="text-white">{{ $spotlightBook->author?->name }}</strong>
                            &bull; {{ $spotlightBook->publisher ?: 'NXB Tri Thức' }}
                            &bull; <span class="text-warning">★ {{ number_format($spotlightBook->rating, 1) }}</span> ({{ $spotlightBook->sold_count }} đã bán)
                        </div>
                        <p class="small opacity-75 mb-4" style="line-height: 1.6;">
                            {{ Str::limit($spotlightBook->description, 155) }}
                        </p>

                        <div class="d-flex flex-wrap align-items-baseline gap-3 mb-4">
                            <span class="fs-3 fw-bold text-warning">{{ number_format($spotlightBook->price, 0, ',', '.') }} đ</span>
                            <span class="text-light opacity-50 text-decoration-line-through small">
                                Giá bìa: {{ number_format($spotlightBook->original_price, 0, ',', '.') }} đ
                            </span>
                            <span class="badge bg-danger rounded-pill px-2 py-1">
                                Tiết kiệm {{ $spotlightBook->discount_badge_percent }}%
                            </span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <form action="{{ route('shop.cart.add', $spotlightBook) }}" method="POST">
                                @csrf
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="btn btn-warning fw-bold px-4 py-2 rounded-3 text-dark shadow-sm">
                                    <i class="bi bi-bag-check-fill me-1"></i> Đặt mua ngay
                                </button>
                            </form>
                            <a href="{{ route('shop.book', $spotlightBook) }}" class="btn btn-outline-light fw-semibold px-4 py-2 rounded-3">
                                Xem giới thiệu sách <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Bìa sách 3D Tiêu điểm -->
                <div class="col-12 col-md-5 d-flex justify-content-center">
                    @if ($spotlightBook)
                        <a href="{{ route('shop.book', $spotlightBook) }}" class="text-decoration-none book-cover-wrap">
                            <div class="book-3d book-3d-lg" style="background: {{ $spotlightBook->cover_gradient }}; transform: rotate(-2deg);">
                                <div class="book-3d-imprint">{{ $spotlightBook->publisher ?: 'NXB TRI THỨC' }}</div>
                                <div class="book-3d-title">{{ $spotlightBook->title }}</div>
                                <div class="book-3d-emblem">
                                    <i class="bi {{ $spotlightBook->category?->icon ?: 'bi-book' }}"></i>
                                </div>
                                <div class="book-3d-author">{{ $spotlightBook->author?->name }}</div>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Bảng Xếp Hạng Sách Bán Chạy (4 cột) -->
    <div class="col-12 col-lg-4">
        <div class="shop-card h-100 p-3 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h6 class="fw-bold mb-0 text-uppercase" style="font-size: 0.84rem; letter-spacing: 0.03em;">
                        <i class="bi bi-trophy-fill text-warning me-1"></i> Top Sách Bán Chạy Tuần
                    </h6>
                    <span class="badge bg-danger-subtle text-danger rounded-pill">Bestseller</span>
                </div>

                <div class="d-grid gap-3">
                    @foreach ($featuredBooks->take(3) as $idx => $topBook)
                        <div class="d-flex align-items-center gap-3">
                            <div class="fw-bold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 26px; height: 26px; font-size: 0.78rem;
                                 background: {{ $idx === 0 ? '#dc2626' : ($idx === 1 ? '#d97706' : '#52525b') }}; color: #fff;">
                                {{ $idx + 1 }}
                            </div>
                            <a href="{{ route('shop.book', $topBook) }}" class="text-decoration-none flex-shrink-0">
                                <div class="book-3d" style="width: 48px; height: 68px; padding: 4px 4px 4px 8px; background: {{ $topBook->cover_gradient }};">
                                    <div style="font-size: 0.42rem; font-weight: 700; line-height: 1.1; overflow: hidden;">
                                        {{ Str::limit($topBook->title, 18) }}
                                    </div>
                                    <div style="font-size: 0.38rem; opacity: 0.8;">★ {{ $topBook->rating }}</div>
                                </div>
                            </a>
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('shop.book', $topBook) }}" class="text-dark fw-bold text-decoration-none d-block text-truncate small">
                                    {{ $topBook->title }}
                                </a>
                                <div class="text-secondary" style="font-size: 0.74rem;">{{ $topBook->author?->name }}</div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="fw-bold text-danger small">{{ number_format($topBook->price, 0, ',', '.') }} đ</span>
                                    <span class="text-secondary" style="font-size: 0.7rem;">Đã bán {{ $topBook->sold_count }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-3 rounded-3 mt-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-gem fs-4 text-warning"></i>
                    <div class="lh-sm">
                        <div class="fw-bold text-dark" style="font-size: 0.8rem;">Đặc quyền Thẻ Hội viên BookVerse</div>
                        <small class="text-secondary" style="font-size: 0.72rem;">
                            Nhập SĐT khi thanh toán để giảm trực tiếp <strong>3% – 10%</strong> trên tổng đơn.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thanh 4 Cam kết Dịch vụ Nhà Sách -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="shop-card p-3 d-flex align-items-center gap-3 h-100">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center text-danger flex-shrink-0"
                  style="width:42px;height:42px;background:#fef2f2;">
                <i class="bi bi-patch-check-fill fs-5"></i>
            </span>
            <div class="lh-sm">
                <div class="fw-bold small">100% Sách Thật NXB</div>
                <small class="text-secondary" style="font-size:0.74rem;">Hoàn tiền 200% nếu sách giả</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="shop-card p-3 d-flex align-items-center gap-3 h-100">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center text-warning flex-shrink-0"
                  style="width:42px;height:42px;background:#fffbeb;">
                <i class="bi bi-journal-check fs-5"></i>
            </span>
            <div class="lh-sm">
                <div class="fw-bold small">Bọc Plastic Cẩn Thận</div>
                <small class="text-secondary" style="font-size:0.74rem;">Đóng hộp chống sốc 3 lớp</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="shop-card p-3 d-flex align-items-center gap-3 h-100">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center text-success flex-shrink-0"
                  style="width:42px;height:42px;background:#f0fdf4;">
                <i class="bi bi-stars fs-5"></i>
            </span>
            <div class="lh-sm">
                <div class="fw-bold small">Chiết Khấu VIP Tự Động</div>
                <small class="text-secondary" style="font-size:0.74rem;">Giảm tới 10% chỉ bằng SĐT</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="shop-card p-3 d-flex align-items-center gap-3 h-100">
            <span class="rounded-3 d-inline-flex align-items-center justify-content-center text-primary flex-shrink-0"
                  style="width:42px;height:42px;background:#eff6ff;">
                <i class="bi bi-arrow-repeat fs-5"></i>
            </span>
            <div class="lh-sm">
                <div class="fw-bold small">Đổi Trả Trong 30 Ngày</div>
                <small class="text-secondary" style="font-size:0.74rem;">Miễn phí nếu lỗi in ấn</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Bố cục 2 Cột Chuẩn Nhà Sách Thương Mại Điện Tử: Sidebar Bộ Lọc (3 cột) + Kệ Sách (9 cột) -->
<div class="row g-4" id="catalogSection">
    <!-- Cột Trái: Danh mục Thể loại & Tác giả -->
    <div class="col-12 col-lg-3">
        <!-- Khung Danh mục Thể loại -->
        <div class="shop-card p-3 mb-3">
            <h6 class="fw-bold text-uppercase small border-bottom pb-2 mb-2">
                <i class="bi bi-bookmarks-fill text-danger me-1"></i> Tủ Sách Theo Thể Loại
            </h6>
            <div class="list-group list-group-flush small">
                <a href="{{ route('shop.index', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-2 border-0 rounded-2 {{ !request('category_id') ? 'bg-danger-subtle text-danger fw-bold' : '' }}">
                    <span><i class="bi bi-collection me-2"></i>Tất cả đầu sách</span>
                    <span class="badge rounded-pill {{ !request('category_id') ? 'bg-danger' : 'bg-secondary-subtle text-secondary' }}">
                        {{ $categories->sum('books_count') }}
                    </span>
                </a>
                @foreach ($categories as $cat)
                    @php $isActiveCat = (string) request('category_id') === (string) $cat->id; @endphp
                    <a href="{{ route('shop.index', array_filter(['category_id' => $cat->id, 'q' => request('q'), 'sort' => request('sort')])) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-2 border-0 rounded-2 {{ $isActiveCat ? 'bg-danger-subtle text-danger fw-bold' : '' }}">
                        <span><i class="bi {{ $cat->icon }} me-2"></i>{{ $cat->name }}</span>
                        <span class="badge rounded-pill {{ $isActiveCat ? 'bg-danger' : 'bg-secondary-subtle text-secondary' }}">
                            {{ $cat->books_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Khung Lọc theo Tác giả -->
        <div class="shop-card p-3 mb-3">
            <h6 class="fw-bold text-uppercase small border-bottom pb-2 mb-2">
                <i class="bi bi-Feather text-danger me-1"></i> Tác Giả Tiêu Biểu
            </h6>
            <div class="list-group list-group-flush small">
                <a href="{{ route('shop.index', array_filter(['category_id' => request('category_id'), 'q' => request('q'), 'sort' => request('sort')])) }}"
                   class="list-group-item list-group-item-action px-2 py-1 border-0 rounded-2 {{ !request('author_id') ? 'fw-bold text-danger' : 'text-secondary' }}">
                    • Tất cả tác giả
                </a>
                @foreach ($authors as $au)
                    @php $isActiveAu = (string) request('author_id') === (string) $au->id; @endphp
                    <a href="{{ route('shop.index', array_filter(['category_id' => request('category_id'), 'author_id' => $au->id, 'q' => request('q'), 'sort' => request('sort')])) }}"
                       class="list-group-item list-group-item-action px-2 py-1 border-0 rounded-2 {{ $isActiveAu ? 'bg-danger-subtle text-danger fw-bold' : 'text-secondary' }}">
                        • {{ $au->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Khung Hướng dẫn Hội viên VIP -->
        <div class="shop-card p-3" style="background: linear-gradient(180deg, #fff 0%, #fef2f2 100%);">
            <h6 class="fw-bold small text-danger mb-2">
                <i class="bi bi-card-checklist me-1"></i> Bảng Ưu Đãi Thẻ Hội Viên
            </h6>
            <ul class="list-unstyled small mb-0 d-grid gap-1 text-secondary" style="font-size: 0.78rem;">
                <li class="d-flex justify-content-between"><span>• Hạng Đồng (Mới):</span><strong>Tích điểm 1%</strong></li>
                <li class="d-flex justify-content-between"><span>• Hạng Bạc (&ge; 500k):</span><strong class="text-primary">Giảm 3%</strong></li>
                <li class="d-flex justify-content-between"><span>• Hạng Vàng (&ge; 2Tr):</span><strong class="text-warning">Giảm 5%</strong></li>
                <li class="d-flex justify-content-between"><span>• Hạng Kim Cương (&ge; 5Tr):</span><strong class="text-danger">Giảm 10%</strong></li>
            </ul>
        </div>
    </div>

    <!-- Cột Phải: Thanh Sắp Xếp & Lưới Sản Phẩm Sách -->
    <div class="col-12 col-lg-9">
        <!-- Thanh tiêu đề kệ sách & Sắp xếp -->
        <div class="shop-card p-3 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="font-serif-book fw-bold mb-0">
                        @if ($currentCategory)
                            Tủ sách: {{ $currentCategory->name }}
                        @elseif ($currentAuthor)
                            Tác giả: {{ $currentAuthor->name }}
                        @elseif (request('q'))
                            Kết quả tìm kiếm: "{{ request('q') }}"
                        @else
                            Kệ Sách Tuyển Chọn
                        @endif
                    </h5>
                    <small class="text-secondary">
                        Hiển thị <strong>{{ $books->count() }}</strong> / <strong>{{ $books->total() }}</strong> ấn phẩm chính hãng
                        @if (request('q') || request('category_id') || request('author_id'))
                            &bull; <a href="{{ route('shop.index') }}" class="text-danger fw-semibold text-decoration-none">Xóa bộ lọc</a>
                        @endif
                    </small>
                </div>

                <!-- Các tab sắp xếp nhanh -->
                <div class="d-flex flex-wrap align-items-center gap-1">
                    @php
                        $sortOptions = [
                            'popular' => 'Bán chạy',
                            'rating' => 'Đánh giá cao ★',
                            'newest' => 'Mới nhất',
                            'price_asc' => 'Giá thấp → cao',
                            'price_desc' => 'Giá cao → thấp',
                        ];
                        $activeSort = request('sort', 'popular');
                    @endphp
                    @foreach ($sortOptions as $sortKey => $sortLabel)
                        <a href="{{ route('shop.index', array_merge(request()->query(), ['sort' => $sortKey, 'page' => null])) }}"
                           class="btn btn-sm rounded-2 {{ $activeSort === $sortKey ? 'btn-bv-primary' : 'btn-light border text-secondary' }}"
                           style="font-size: 0.78rem;">
                            {{ $sortLabel }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lưới Sách (4 cột trên màn hình lớn) -->
        <div class="row g-3">
            @forelse ($books as $book)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="shop-product-card h-100 d-flex flex-column justify-content-between p-3 position-relative">
                        <!-- Nhãn Giảm Giá & Hot -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            @if ($book->is_featured)
                                <span class="badge bg-warning text-dark" style="font-size: 0.68rem;">
                                    <i class="bi bi-fire"></i> Bestseller
                                </span>
                            @else
                                <span class="badge bg-light text-secondary border" style="font-size: 0.66rem;">
                                    {{ $book->publisher ?: 'NXB Tri Thức' }}
                                </span>
                            @endif

                            @if ($book->discount_badge_percent > 0)
                                <span class="badge-discount">-{{ $book->discount_badge_percent }}%</span>
                            @endif
                        </div>

                        <!-- Bìa Sách 3D Chân Thực -->
                        <div class="text-center py-3 mb-2 rounded-3" style="background: #faf8f5;">
                            <a href="{{ route('shop.book', $book) }}" class="text-decoration-none book-cover-wrap">
                                <div class="book-3d" style="background: {{ $book->cover_gradient }};">
                                    <div class="book-3d-imprint">{{ Str::limit($book->publisher ?: 'BOOKVERSE', 16) }}</div>
                                    <div class="book-3d-title">{{ $book->title }}</div>
                                    <div class="book-3d-emblem">
                                        <i class="bi {{ $book->category?->icon ?: 'bi-journal-richtext' }}"></i>
                                    </div>
                                    <div class="book-3d-author">{{ $book->author?->name }}</div>
                                </div>
                            </a>
                        </div>

                        <!-- Thông tin Sách -->
                        <div class="flex-grow-1 d-flex flex-column justify-content-between">
                            <div>
                                <div class="text-secondary text-truncate mb-1" style="font-size: 0.74rem;">
                                    {{ $book->author?->name }} &bull; {{ $book->category?->name }}
                                </div>
                                <a href="{{ route('shop.book', $book) }}" class="book-card-title mb-2" title="{{ $book->title }}">
                                    {{ $book->title }}
                                </a>
                            </div>

                            <div>
                                <!-- Đánh giá sao & Đã bán -->
                                <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 0.74rem;">
                                    <span class="text-warning fw-bold">
                                        <i class="bi bi-star-fill"></i> {{ number_format($book->rating, 1) }}
                                    </span>
                                    <span class="text-secondary">Đã bán {{ $book->sold_count }}</span>
                                </div>

                                <!-- Giá bán & Giá bìa -->
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <span class="price-sale">{{ number_format($book->price, 0, ',', '.') }} đ</span>
                                    <span class="price-old">{{ number_format($book->original_price, 0, ',', '.') }} đ</span>
                                </div>

                                <!-- Nút Đặt Mua -->
                                @if ($book->stock > 0)
                                    <div class="d-flex gap-1">
                                        <form action="{{ route('shop.cart.add', $book) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-bv-outline btn-sm w-100 rounded-2 py-1" style="font-size: 0.78rem;">
                                                <i class="bi bi-cart-plus"></i> Thêm giỏ
                                            </button>
                                        </form>
                                        <form action="{{ route('shop.cart.add', $book) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="buy_now" value="1">
                                            <button type="submit" class="btn btn-bv-primary btn-sm rounded-2 px-2 py-1" style="font-size: 0.78rem;">
                                                Mua
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100 rounded-2 py-1" disabled style="font-size: 0.78rem;">
                                        Tạm hết hàng
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="shop-card p-5 text-center">
                        <i class="bi bi-journal-x fs-1 text-secondary d-block mb-2"></i>
                        <h6 class="fw-bold">Không tìm thấy cuốn sách nào phù hợp với bộ lọc</h6>
                        <p class="text-secondary small mb-3">Bạn hãy thử tìm bằng từ khóa khác hoặc xem toàn bộ kệ sách của BookVerse.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-bv-primary btn-sm rounded-pill px-4">Xem tất cả sách</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Phân trang -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
