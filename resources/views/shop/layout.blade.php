<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookVerse.vn - Nhà Sách Trực Tuyến & Xuất Bản Phẩm Chính Hãng')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bv-primary: #b91c1c;
            --bv-primary-dark: #991b1b;
            --bv-primary-soft: #fef2f2;
            --bv-gold: #d97706;
            --bv-ink: #18181b;
            --bv-paper: #f9f7f2;
            --bv-border: #e5e0d5;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bv-paper);
            color: var(--bv-ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .font-serif-book {
            font-family: 'Lora', Georgia, serif;
        }
        /* Top utility bar */
        .bv-topbar {
            background: #18181b;
            color: #e4e4e7;
            font-size: 0.78rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .bv-topbar a {
            color: #f4f4f5;
            text-decoration: none;
        }
        .bv-topbar a:hover {
            color: #fbbf24;
        }
        /* Main bookstore header */
        .shop-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--bv-border);
            box-shadow: 0 4px 20px rgba(24, 24, 27, 0.04);
        }
        .bv-category-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--bv-border);
            font-size: 0.86rem;
        }
        .bv-category-bar .nav-link {
            color: #3f3f46;
            font-weight: 600;
            padding: 0.65rem 0.9rem;
            border-bottom: 2px solid transparent;
            transition: all 0.15s ease;
        }
        .bv-category-bar .nav-link:hover,
        .bv-category-bar .nav-link.active {
            color: var(--bv-primary);
            border-bottom-color: var(--bv-primary);
        }
        /* Search Box */
        .bv-search-group {
            border: 2px solid var(--bv-primary);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }
        .bv-search-group .form-control {
            border: 0;
            box-shadow: none !important;
            font-size: 0.92rem;
            padding: 0.55rem 0.9rem;
        }
        .bv-search-group .btn-search {
            background: var(--bv-primary);
            color: #fff;
            border: 0;
            padding: 0 1.25rem;
            font-weight: 700;
        }
        .bv-search-group .btn-search:hover {
            background: var(--bv-primary-dark);
        }
        /* Cards */
        .shop-card {
            background: #ffffff;
            border: 1px solid var(--bv-border);
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .shop-product-card {
            background: #ffffff;
            border: 1px solid var(--bv-border);
            border-radius: 12px;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            overflow: hidden;
        }
        .shop-product-card:hover {
            transform: translateY(-4px);
            border-color: #d6cbb9;
            box-shadow: 0 14px 28px rgba(24, 24, 27, 0.09);
        }
        /* Realistic Publisher Book Cover */
        .book-cover-wrap {
            position: relative;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            perspective: 800px;
        }
        .book-3d {
            width: 132px;
            height: 192px;
            border-radius: 3px 7px 7px 3px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px 10px 10px 16px;
            color: #fff;
            text-align: center;
            box-shadow:
                inset 5px 0 10px rgba(0, 0, 0, 0.28),
                inset -1px 0 2px rgba(255, 255, 255, 0.25),
                3px 2px 0 #f5f2eb,
                4px 3px 0 #d4d0c8,
                6px 5px 0 #f5f2eb,
                7px 6px 0 #c7c2b8,
                8px 12px 20px rgba(15, 23, 42, 0.24);
            flex-shrink: 0;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: hidden;
        }
        .shop-product-card:hover .book-3d {
            transform: rotateY(-6deg) scale(1.03);
        }
        /* Spine groove */
        .book-3d::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(255, 255, 255, 0.28);
            box-shadow: -2px 0 3px rgba(0, 0, 0, 0.35);
            z-index: 2;
        }
        /* Inner ornamental frame on book cover */
        .book-3d::after {
            content: '';
            position: absolute;
            top: 6px;
            right: 6px;
            bottom: 6px;
            left: 13px;
            border: 1px solid rgba(255, 255, 255, 0.26);
            border-radius: 2px 4px 4px 2px;
            pointer-events: none;
        }
        .book-3d-imprint {
            font-size: 0.52rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.85;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.22);
            padding-bottom: 3px;
            margin-bottom: 4px;
        }
        .book-3d-title {
            font-family: 'Lora', Georgia, serif;
            font-weight: 700;
            font-size: 0.78rem;
            line-height: 1.24;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
        }
        .book-3d-emblem {
            width: 34px;
            height: 34px;
            margin: 4px auto;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }
        .book-3d-author {
            font-size: 0.6rem;
            font-weight: 600;
            opacity: 0.92;
            border-top: 1px solid rgba(255, 255, 255, 0.22);
            padding-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Large cover on detail page */
        .book-3d-lg {
            width: 215px;
            height: 310px;
            padding: 18px 16px 18px 24px;
            border-radius: 4px 10px 10px 4px;
            box-shadow:
                inset 7px 0 14px rgba(0, 0, 0, 0.32),
                inset -1px 0 2px rgba(255, 255, 255, 0.3),
                4px 3px 0 #f5f2eb,
                6px 5px 0 #d4d0c8,
                9px 7px 0 #f5f2eb,
                11px 9px 0 #c7c2b8,
                14px 18px 30px rgba(15, 23, 42, 0.28);
        }
        .book-3d-lg .book-3d-imprint {
            font-size: 0.68rem;
            padding-bottom: 6px;
        }
        .book-3d-lg .book-3d-title {
            font-size: 1.15rem;
            -webkit-line-clamp: 4;
        }
        .book-3d-lg .book-3d-emblem {
            width: 56px;
            height: 56px;
            font-size: 1.45rem;
        }
        .book-3d-lg .book-3d-author {
            font-size: 0.78rem;
            padding-top: 6px;
        }
        /* Clamp product title to 2 lines */
        .book-card-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.6em;
            line-height: 1.3;
            font-size: 0.94rem;
            font-weight: 700;
            color: #18181b;
            text-decoration: none;
        }
        .book-card-title:hover {
            color: var(--bv-primary);
        }
        .price-sale {
            color: var(--bv-primary);
            font-weight: 800;
            font-size: 1.12rem;
        }
        .price-old {
            color: #9ca3af;
            text-decoration: line-through;
            font-size: 0.78rem;
        }
        .badge-discount {
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 700;
            font-size: 0.72rem;
            padding: 2px 6px;
            border-radius: 5px;
        }
        .btn-bv-primary {
            background: var(--bv-primary);
            color: #fff;
            border: 1px solid var(--bv-primary);
            font-weight: 700;
        }
        .btn-bv-primary:hover {
            background: var(--bv-primary-dark);
            color: #fff;
            border-color: var(--bv-primary-dark);
        }
        .btn-bv-outline {
            background: #fff;
            color: var(--bv-primary);
            border: 1px solid var(--bv-primary);
            font-weight: 600;
        }
        .btn-bv-outline:hover {
            background: var(--bv-primary-soft);
            color: var(--bv-primary-dark);
            border-color: var(--bv-primary-dark);
        }
        .pagination {
            gap: 0.35rem;
        }
        .pagination .page-link {
            border-radius: 8px !important;
            font-weight: 600;
            padding: 0.45rem 0.85rem;
            color: #18181b;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--bv-primary);
            border-color: var(--bv-primary);
            color: #fff;
        }
        nav[role="navigation"] svg {
            width: 1.1rem;
            height: 1.1rem;
        }
    </style>
</head>
<body>
    @php
        $cart = session('shop_cart', []);
        $cartCount = array_sum($cart);
        $navCategories = \App\Models\Category::withCount('books')->orderBy('name')->get();
    @endphp

    <!-- Top Service Bar (Chuẩn Nhà Sách Thương Mại Điện Tử) -->
    <div class="bv-topbar py-1">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span><i class="bi bi-patch-check-fill text-warning me-1"></i> 100% Sách Bản Quyền NXB</span>
                <span class="d-none d-md-inline text-secondary">|</span>
                <span class="d-none d-md-inline"><i class="bi bi-truck text-warning me-1"></i> Miễn phí giao hàng toàn quốc</span>
                <span class="d-none d-lg-inline text-secondary">|</span>
                <span class="d-none d-lg-inline"><i class="bi bi-stars text-warning me-1"></i> Nhập SĐT giảm ngay tới <strong>10%</strong> cho Hội viên VIP</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="tel:19006886"><i class="bi bi-headset me-1 text-warning"></i> Hotline: <strong>1900 6886</strong></a>
                <span class="text-secondary">|</span>
                <a href="{{ route('shop.track') }}"><i class="bi bi-box-seam me-1"></i> Kiểm tra đơn hàng</a>
                <span class="text-secondary">|</span>
                <a href="{{ route('dashboard') }}" class="text-warning fw-semibold">
                    <i class="bi bi-shield-lock-fill me-1"></i> Cổng Quản trị & POS
                </a>
            </div>
        </div>
    </div>

    <!-- Main Bookstore Header -->
    <header class="shop-navbar sticky-top py-3">
        <div class="container">
            <div class="row align-items-center g-3">
                <!-- Brand Logo -->
                <div class="col-6 col-lg-3">
                    <a href="{{ route('shop.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <span class="rounded-3 d-inline-flex align-items-center justify-content-center text-white shadow-sm"
                              style="width:44px;height:44px;background:linear-gradient(135deg,#b91c1c,#7f1d1d);">
                            <i class="bi bi-journal-bookmark-fill fs-4"></i>
                        </span>
                        <div>
                            <div class="font-serif-book fw-bold fs-4 text-dark lh-1">BookVerse<span class="text-danger">.vn</span></div>
                            <small class="text-secondary" style="font-size:0.7rem;letter-spacing:0.04em;">NHÀ SÁCH TRỰC TUYẾN</small>
                        </div>
                    </a>
                </div>

                <!-- Smart Search Box + Quick Keywords -->
                <div class="col-12 col-lg-6 order-3 order-lg-2">
                    <form action="{{ route('shop.index') }}" method="GET">
                        @if (request('category_id'))
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        @endif
                        <div class="input-group bv-search-group">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                   placeholder="Tìm kiếm tựa sách, tác giả, nhà xuất bản hoặc mã ISBN...">
                            <button type="submit" class="btn-search">
                                <i class="bi bi-search me-1"></i> Tìm sách
                            </button>
                        </div>
                    </form>
                    <div class="d-none d-md-flex flex-wrap align-items-center gap-2 mt-1 small" style="font-size:0.74rem;">
                        <span class="text-secondary">Từ khóa phổ biến:</span>
                        <a href="{{ route('shop.index', ['q' => 'Clean Code']) }}" class="text-secondary text-decoration-none">Clean Code</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('shop.index', ['q' => 'Nhà Giả Kim']) }}" class="text-secondary text-decoration-none">Nhà Giả Kim</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('shop.index', ['q' => 'Tư Duy']) }}" class="text-secondary text-decoration-none">Tư Duy Nhanh Và Chậm</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('shop.index', ['sort' => 'rating']) }}" class="text-danger fw-semibold text-decoration-none">Sách 5 Sao ★</a>
                    </div>
                </div>

                <!-- Right Actions: Track Order + Cart -->
                <div class="col-6 col-lg-3 order-2 order-lg-3 d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('shop.track') }}" class="btn btn-light border rounded-3 px-3 py-2 d-none d-xl-inline-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-truck fs-5 text-danger"></i>
                        <div class="text-start lh-1">
                            <small class="text-secondary d-block" style="font-size:0.68rem;">Đơn hàng</small>
                            <span class="fw-bold" style="font-size:0.8rem;">Tra cứu</span>
                        </div>
                    </a>

                    <a href="{{ route('shop.cart') }}" class="btn btn-bv-primary rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 position-relative">
                        <i class="bi bi-bag-check-fill fs-5"></i>
                        <div class="text-start lh-1">
                            <small class="opacity-75 d-block" style="font-size:0.68rem;">Giỏ sách</small>
                            <span class="fw-bold" style="font-size:0.82rem;">{{ $cartCount }} cuốn</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Category Navigation Bar -->
    <nav class="bv-category-bar d-none d-md-block">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="nav">
                <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.index') && !request('category_id') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill me-1 text-danger"></i> Tất cả Danh mục
                </a>
                @foreach ($navCategories as $navCat)
                    <a href="{{ route('shop.index', ['category_id' => $navCat->id]) }}"
                       class="nav-link {{ (string) request('category_id') === (string) $navCat->id ? 'active' : '' }}">
                        <i class="bi {{ $navCat->icon }} me-1"></i> {{ $navCat->name }}
                    </a>
                @endforeach
            </div>
            <div class="small text-secondary d-none d-lg-block">
                <i class="bi bi-award-fill text-warning me-1"></i> Hạng Kim Cương giảm <strong>10%</strong> &bull; Hạng Vàng giảm <strong>5%</strong>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Professional Bookstore Footer -->
    <footer class="bg-white border-top mt-5 pt-5 pb-4">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom">
                <div class="col-12 col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="rounded-3 d-inline-flex align-items-center justify-content-center text-white"
                              style="width:38px;height:38px;background:linear-gradient(135deg,#b91c1c,#7f1d1d);">
                            <i class="bi bi-journal-bookmark-fill fs-5"></i>
                        </span>
                        <span class="font-serif-book fw-bold fs-4 text-dark">BookVerse<span class="text-danger">.vn</span></span>
                    </div>
                    <p class="small text-secondary mb-3">
                        Hệ thống Nhà sách Trực tuyến & Xuất bản phẩm Chính hãng. Cung cấp hàng ngàn đầu sách Công nghệ, Kinh tế, Văn học và Kỹ năng sống với cam kết 100% sách thật từ Nhà xuất bản.
                    </p>
                    <div class="small text-secondary">
                        <div class="mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Cơ sở chính: Trịnh Văn Bô, Nam Từ Liêm, Hà Nội & Bắc Ninh</div>
                        <div class="mb-1"><i class="bi bi-telephone-fill text-danger me-2"></i>Hotline CSKH: <strong>1900 6886</strong> (08:00 - 21:30)</div>
                        <div><i class="bi bi-envelope-fill text-danger me-2"></i>Email: cskh@bookverse.vn</div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <h6 class="fw-bold text-uppercase small mb-3">Danh mục Sách</h6>
                    <ul class="list-unstyled small d-grid gap-2">
                        @foreach ($navCategories as $navCat)
                            <li>
                                <a href="{{ route('shop.index', ['category_id' => $navCat->id]) }}" class="text-secondary text-decoration-none">
                                    {{ $navCat->name }} ({{ $navCat->books_count }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <h6 class="fw-bold text-uppercase small mb-3">Hỗ trợ Độc giả</h6>
                    <ul class="list-unstyled small d-grid gap-2 text-secondary">
                        <li><a href="{{ route('shop.track') }}" class="text-secondary text-decoration-none">Tra cứu trạng thái đơn hàng</a></li>
                        <li><a href="{{ route('shop.cart') }}" class="text-secondary text-decoration-none">Giỏ sách & Kiểm tra thẻ VIP</a></li>
                        <li>Chính sách đổi trả 30 ngày miễn phí</li>
                        <li>Chính sách tích điểm Hội viên BookVerse</li>
                        <li><a href="{{ route('dashboard') }}" class="text-danger fw-semibold text-decoration-none"><i class="bi bi-speedometer2 me-1"></i>Dành cho Quản trị viên & POS</a></li>
                    </ul>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <h6 class="fw-bold text-uppercase small mb-3">Phương thức Thanh toán</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-qr-code-scan text-danger me-1"></i> VietQR 24/7</span>
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-cash-coin text-success me-1"></i> Tiền mặt COD</span>
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-credit-card text-primary me-1"></i> Visa / ATM</span>
                    </div>
                    <div class="p-3 rounded-3 small" style="background: var(--bv-primary-soft); border: 1px dashed #fca5a5;">
                        <div class="fw-bold text-danger mb-1"><i class="bi bi-gem me-1"></i> Mẹo thử nghiệm Hội viên VIP:</div>
                        <div class="text-secondary" style="font-size:0.76rem;">
                            Nhập SĐT <code>0901234567</code> khi thanh toán để test tự động giảm <strong>10% (Hạng Kim Cương)</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 small text-secondary">
                <div>&copy; {{ date('Y') }} <strong>BookVerse Bookstore</strong> — Thực hiện bởi Sinh viên Vũ Minh Hiếu (DCCNTT 13.10.16).</div>
                <div>Laravel Framework 12 &bull; PHP 8.2 &bull; MySQL (XAMPP)</div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
