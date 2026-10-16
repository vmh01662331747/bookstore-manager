@extends('shop.layout')

@section('title', $book->title . ' - BookVerse.vn')

@section('content')
<!-- Breadcrumb chuẩn trang thương mại điện tử -->
<nav aria-label="breadcrumb" class="mb-3 small">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-secondary">Trang chủ</a></li>
        @if ($book->category)
            <li class="breadcrumb-item">
                <a href="{{ route('shop.index', ['category_id' => $book->category_id]) }}" class="text-decoration-none text-secondary">
                    {{ $book->category->name }}
                </a>
            </li>
        @endif
        <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ Str::limit($book->title, 50) }}</li>
    </ol>
</nav>

<!-- Khối Chi tiết Sản phẩm Sách (3 Cột chuẩn Nhà sách trực tuyến) -->
<div class="shop-card p-4 mb-4">
    <div class="row g-4">
        <!-- Cột 1: Bìa sách 3D cỡ lớn -->
        <div class="col-12 col-md-4 text-center">
            <div class="p-4 rounded-3 mb-3 d-flex justify-content-center align-items-center" style="background: #faf8f5; min-height: 360px;">
                <div class="book-3d book-3d-lg" style="background: {{ $book->cover_gradient }};">
                    <div class="book-3d-imprint">{{ $book->publisher ?: 'NXB TRI THỨC' }}</div>
                    <div class="book-3d-title">{{ $book->title }}</div>
                    <div class="book-3d-emblem">
                        <i class="bi {{ $book->category?->icon ?: 'bi-journal-richtext' }}"></i>
                    </div>
                    <div class="book-3d-author">{{ $book->author?->name }}</div>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-2 small text-secondary">
                <span class="badge bg-light text-dark border"><i class="bi bi-check2-circle text-success me-1"></i>Ấn bản chính hãng</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-book text-danger me-1"></i>{{ $book->pages }} trang</span>
            </div>
        </div>

        <!-- Cột 2: Thông tin Giá bán & Đặt mua -->
        <div class="col-12 col-md-5">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">{{ $book->category?->name }}</span>
                @if ($book->is_featured)
                    <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-fire me-1"></i>Sách Bán Chạy</span>
                @endif
            </div>

            <h1 class="font-serif-book fw-bold h3 mb-2">{{ $book->title }}</h1>

            <div class="small text-secondary mb-3">
                Tác giả:
                <a href="{{ route('shop.index', ['author_id' => $book->author_id]) }}" class="fw-bold text-dark text-decoration-none">
                    {{ $book->author?->name }}
                </a>
                <span class="mx-2">|</span>
                NXB: <strong class="text-dark">{{ $book->publisher ?: 'NXB Tri Thức' }}</strong>
            </div>

            <div class="d-flex align-items-center gap-3 small mb-3 pb-3 border-bottom">
                <span class="text-warning fw-bold">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= round($book->rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                    <span class="text-dark ms-1">{{ number_format($book->rating, 1) }}</span>
                </span>
                <span class="text-secondary">|</span>
                <span class="text-secondary">Đã bán: <strong class="text-dark">{{ $book->sold_count }}</strong> cuốn</span>
                <span class="text-secondary">|</span>
                <span class="font-monospace text-secondary">ISBN: {{ $book->isbn }}</span>
            </div>

            <!-- Khung giá niêm yết & tiết kiệm -->
            <div class="p-3 rounded-3 mb-4" style="background: #fef2f2; border: 1px solid #fecaca;">
                <div class="d-flex align-items-baseline gap-3">
                    <span class="fs-2 fw-bold text-danger">{{ number_format($book->price, 0, ',', '.') }} đ</span>
                    <span class="text-secondary text-decoration-line-through small">
                        {{ number_format($book->original_price, 0, ',', '.') }} đ
                    </span>
                    @if ($book->discount_badge_percent > 0)
                        <span class="badge bg-danger">-{{ $book->discount_badge_percent }}%</span>
                    @endif
                </div>
                <div class="small text-secondary mt-1">
                    Tiết kiệm: <strong class="text-danger">{{ number_format($book->original_price - $book->price, 0, ',', '.') }} đ</strong> so với giá bìa (Giảm thêm tới 10% cho Hội viên VIP)
                </div>
            </div>

            @if ($book->stock > 0)
                <form action="{{ route('shop.cart.add', $book) }}" method="POST">
                    @csrf
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="small fw-semibold text-secondary">Số lượng:</span>
                        <div class="input-group input-group-sm" style="width: 125px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="const el=this.nextElementSibling;if(el.value>1)el.value--;"><i class="bi bi-dash"></i></button>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $book->stock }}" class="form-control text-center fw-bold">
                            <button type="button" class="btn btn-outline-secondary" onclick="const el=this.previousElementSibling;if(Number(el.value)<{{ $book->stock }})el.value++;"><i class="bi bi-plus"></i></button>
                        </div>
                        <span class="small text-success fw-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i>Còn {{ $book->stock }} cuốn trong kho
                        </span>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-bv-outline px-4 py-2 rounded-3">
                            <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ sách
                        </button>
                        <button type="submit" name="buy_now" value="1" class="btn btn-bv-primary px-4 py-2 rounded-3 shadow-sm">
                            <i class="bi bi-bag-check-fill me-1"></i> Đặt mua ngay
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-secondary rounded-3 mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i> Ấn phẩm này hiện đang tạm hết hàng.
                </div>
            @endif
        </div>

        <!-- Cột 3: Cam kết của Nhà sách BookVerse -->
        <div class="col-12 col-md-3">
            <div class="p-3 rounded-3 h-100" style="background: #faf8f5; border: 1px solid #e5e0d5;">
                <h6 class="fw-bold small text-uppercase border-bottom pb-2 mb-3">
                    <i class="bi bi-shield-check text-danger me-1"></i> Dịch vụ tại BookVerse
                </h6>
                <ul class="list-unstyled small d-grid gap-3 mb-0">
                    <li class="d-flex gap-2">
                        <i class="bi bi-patch-check-fill text-danger fs-6 flex-shrink-0"></i>
                        <span><strong>Sách bản quyền 100%:</strong> Nhập trực tiếp từ {{ $book->publisher ?: 'NXB Tri Thức' }}.</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-gem text-warning fs-6 flex-shrink-0"></i>
                        <span><strong>Ưu đãi VIP theo SĐT:</strong> Tự động giảm thêm 3% – 10% khi thanh toán.</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-box-seam text-primary fs-6 flex-shrink-0"></i>
                        <span><strong>Đóng gói tiêu chuẩn:</strong> Bọc chống ẩm và hộp carton 3 lớp bảo vệ góc sách.</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-arrow-repeat text-success fs-6 flex-shrink-0"></i>
                        <span><strong>Đổi trả 30 ngày:</strong> Đổi mới miễn phí nếu sách lỗi in ấn hoặc hư hỏng khi vận chuyển.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Khối Giới thiệu nội dung & Bảng Thông số Xuất bản -->
<div class="row g-4 mb-5">
    <div class="col-12 col-lg-7">
        <div class="shop-card p-4 h-100">
            <h5 class="font-serif-book fw-bold border-bottom pb-2 mb-3">Giới thiệu nội dung sách</h5>
            <p class="text-secondary mb-3" style="line-height: 1.75;">
                {{ $book->description ?: 'Cuốn sách mang đến những kiến thức nền tảng và góc nhìn sâu sắc, được biên soạn công phu dành cho độc giả yêu thích lĩnh vực ' . ($book->category?->name ?? 'tri thức') . '.' }}
            </p>
            @if ($book->author?->bio)
                <div class="p-3 rounded-3 small mt-3" style="background: #faf8f5; border-left: 3px solid #b91c1c;">
                    <div class="fw-bold text-dark mb-1">Về tác giả {{ $book->author->name }}:</div>
                    <div class="text-secondary">{{ $book->author->bio }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="shop-card p-4 h-100">
            <h5 class="font-serif-book fw-bold border-bottom pb-2 mb-3">Thông tin chi tiết xuất bản</h5>
            <table class="table table-sm table-bordered small mb-0">
                <tbody>
                    <tr>
                        <th class="bg-light text-secondary" style="width: 40%;">Mã hàng (ISBN)</th>
                        <td class="font-monospace fw-semibold">{{ $book->isbn }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-secondary">Tác giả</th>
                        <td class="fw-semibold">{{ $book->author?->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-secondary">Thể loại</th>
                        <td>{{ $book->category?->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-secondary">Nhà xuất bản</th>
                        <td>{{ $book->publisher ?: 'NXB Tri Thức' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-secondary">Năm xuất bản</th>
                        <td>{{ $book->published_year }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-secondary">Số trang</th>
                        <td>{{ $book->pages }} trang (Khổ 14.5 x 20.5 cm)</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-secondary">Hình thức</th>
                        <td>Bìa mềm tay gấp / In giấy chống lóa</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sách cùng thể loại -->
@if ($relatedBooks->isNotEmpty())
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-serif-book fw-bold mb-0">Ấn phẩm cùng thể loại có thể bạn quan tâm</h5>
        <a href="{{ route('shop.index', ['category_id' => $book->category_id]) }}" class="small text-danger fw-semibold text-decoration-none">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="row g-3">
        @foreach ($relatedBooks as $rel)
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="shop-product-card p-3 h-100 d-flex flex-column justify-content-between">
                    <div class="text-center py-3 mb-2 rounded-3" style="background: #faf8f5;">
                        <a href="{{ route('shop.book', $rel) }}" class="text-decoration-none book-cover-wrap">
                            <div class="book-3d" style="background: {{ $rel->cover_gradient }};">
                                <div class="book-3d-imprint">{{ Str::limit($rel->publisher ?: 'BOOKVERSE', 16) }}</div>
                                <div class="book-3d-title">{{ $rel->title }}</div>
                                <div class="book-3d-emblem">
                                    <i class="bi {{ $rel->category?->icon ?: 'bi-book' }}"></i>
                                </div>
                                <div class="book-3d-author">{{ $rel->author?->name }}</div>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('shop.book', $rel) }}" class="book-card-title mb-1">{{ $rel->title }}</a>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="price-sale">{{ number_format($rel->price, 0, ',', '.') }} đ</span>
                            <span class="price-old">{{ number_format($rel->original_price, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
