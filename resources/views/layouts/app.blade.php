<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookVerse AI - Hệ thống Quản lý Bán Sách Thông Minh')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bv-sidebar-width: 268px;
            --bv-primary: #4f46e5;
            --bv-primary-soft: rgba(79, 70, 229, 0.1);
            --bv-bg: #f4f6fb;
            --bv-card-bg: #ffffff;
            --bv-border: rgba(15, 23, 42, 0.08);
        }
        [data-bs-theme="dark"] {
            --bv-bg: #0b1120;
            --bv-card-bg: #111827;
            --bv-border: rgba(255, 255, 255, 0.08);
            --bv-primary-soft: rgba(99, 102, 241, 0.18);
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bv-bg);
            min-height: 100vh;
            overflow-x: hidden;
        }
        .bv-sidebar {
            width: var(--bv-sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1030;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.25s ease;
        }
        .bv-brand {
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: #fff;
        }
        .bv-brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.45);
        }
        .bv-nav-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.72rem 1.1rem;
            margin: 0.2rem 0.85rem;
            border-radius: 11px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.93rem;
            transition: all 0.2s ease;
        }
        .bv-nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.07);
        }
        .bv-nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }
        .bv-main {
            margin-left: var(--bv-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .bv-topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--bv-border);
            padding: 0.85rem 1.75rem;
        }
        [data-bs-theme="dark"] .bv-topbar {
            background: rgba(17, 24, 39, 0.82);
        }
        .bv-card {
            background: var(--bv-card-bg);
            border: 1px solid var(--bv-border);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .bv-card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.09);
        }
        .bv-kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        /* 3D Smart Book Cover */
        .bv-book-cover {
            width: 54px;
            height: 74px;
            border-radius: 6px 10px 10px 6px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 7px 6px 7px 11px;
            color: #fff;
            font-weight: 700;
            font-size: 0.65rem;
            line-height: 1.15;
            box-shadow: -3px 0 0 rgba(0,0,0,0.22) inset, 0 6px 14px rgba(15, 23, 42, 0.18);
            overflow: hidden;
            flex-shrink: 0;
        }
        .bv-book-cover::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(255,255,255,0.3);
        }
        .bv-book-cover-lg {
            width: 150px;
            height: 210px;
            padding: 16px 14px 16px 22px;
            font-size: 0.95rem;
            border-radius: 8px 14px 14px 8px;
        }
        .bv-search-trigger {
            background: var(--bv-bg);
            border: 1px solid var(--bv-border);
            border-radius: 12px;
            padding: 0.48rem 1rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            min-width: 290px;
            transition: border-color 0.2s;
        }
        .bv-search-trigger:hover {
            border-color: var(--bv-primary);
        }
        .pagination {
            gap: 0.35rem;
        }
        .pagination .page-link {
            border-radius: 10px !important;
            font-weight: 600;
            padding: 0.45rem 0.85rem;
        }
        nav[role="navigation"] svg {
            width: 1.1rem;
            height: 1.1rem;
        }
        @media (max-width: 991.98px) {
            .bv-sidebar {
                transform: translateX(-100%);
            }
            .bv-sidebar.show {
                transform: translateX(0);
            }
            .bv-main {
                margin-left: 0;
            }
        }
        @media print {
            .bv-sidebar, .bv-topbar, .no-print {
                display: none !important;
            }
            .bv-main {
                margin-left: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="bv-sidebar" id="bvSidebar">
        <a href="{{ route('dashboard') }}" class="bv-brand">
            <span class="bv-brand-logo"><i class="bi bi-book-half"></i></span>
            <div>
                <div class="fw-bold fs-5 lh-1">BookVerse <span class="badge bg-indigo text-white" style="background:#6366f1;font-size:0.65rem;">AI</span></div>
                <small class="text-secondary" style="font-size: 0.74rem;">Smart Bookstore ERP & POS</small>
            </div>
        </a>

        <div class="px-3 pt-3 pb-1 text-uppercase text-secondary fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.08em;">
            Điều hành & Bán hàng
        </div>
        <nav class="flex-grow-1">
            <a href="{{ route('shop.index') }}" class="bv-nav-link" style="background: rgba(16, 185, 129, 0.14); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.28);">
                <i class="bi bi-globe2"></i>
                <span>Website Mua Sách Online</span>
                <i class="bi bi-box-arrow-up-right ms-auto small"></i>
            </a>
            <a href="{{ route('dashboard') }}" class="bv-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Tổng quan thông minh</span>
            </a>
            <a href="{{ route('orders.create') }}" class="bv-nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                <span>Bán hàng nhanh (POS)</span>
                <span class="badge rounded-pill bg-warning text-dark ms-auto" style="font-size:0.65rem;">Smart</span>
            </a>
            <a href="{{ route('orders.index') }}" class="bv-nav-link {{ request()->routeIs('orders.index', 'orders.show') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Hóa đơn & Đơn hàng</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-uppercase text-secondary fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.08em;">
                Quản trị Kho Tri Thức
            </div>
            <a href="{{ route('books.index') }}" class="bv-nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                <i class="bi bi-journal-richtext"></i>
                <span>Kho sách & Tồn kho</span>
            </a>
            <a href="{{ route('categories.index') }}" class="bv-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i>
                <span>Danh mục thể loại</span>
            </a>
            <a href="{{ route('authors.index') }}" class="bv-nav-link {{ request()->routeIs('authors.*') ? 'active' : '' }}">
                <i class="bi bi-feather"></i>
                <span>Tác giả</span>
            </a>
            <a href="{{ route('customers.index') }}" class="bv-nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Khách hàng & Hội viên VIP</span>
            </a>
        </nav>

        <div class="p-3 m-3 rounded-4" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-stars text-warning"></i>
                <span class="fw-semibold small text-white">Trợ lý Kho Thông Minh</span>
            </div>
            <p class="mb-2 text-secondary" style="font-size: 0.75rem;">
                Nhấn <kbd class="bg-dark border border-secondary">Ctrl + K</kbd> để tra cứu tức thì ISBN, tên sách, SĐT khách hàng.
            </p>
            <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary w-100 fw-semibold rounded-3">
                <i class="bi bi-cart-plus me-1"></i> Tạo đơn bán mới
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="bv-main">
        <header class="bv-topbar d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="bv-search-trigger" data-bs-toggle="modal" data-bs-target="#quickSearchModal">
                    <i class="bi bi-search text-primary"></i>
                    <span class="small">Tìm nhanh sách, ISBN, khách hàng, mã đơn...</span>
                    <kbd class="ms-auto small bg-body-secondary text-body border">Ctrl K</kbd>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('shop.index') }}" class="btn btn-sm btn-success rounded-3 d-inline-flex align-items-center gap-1 fw-semibold">
                    <i class="bi bi-globe2"></i> Xem Website Mua Online
                </a>
                <a href="{{ route('books.create') }}" class="btn btn-sm btn-outline-primary rounded-3 d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg"></i> Thêm sách
                </a>
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary rounded-3 d-inline-flex align-items-center gap-1">
                    <i class="bi bi-bag-check-fill"></i> Mở POS
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" id="themeToggle" title="Đổi giao diện Sáng / Tối">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>
            </div>
        </header>

        <main class="container-fluid px-4 py-4 flex-grow-1">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4">
                    <div class="fw-bold mb-1"><i class="bi bi-x-octagon-fill me-1"></i> Vui lòng kiểm tra lại dữ liệu:</div>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="px-4 py-3 text-center text-secondary small border-top no-print" style="border-color: var(--bv-border) !important;">
            BookVerse AI &copy; {{ date('Y') }} — Hệ thống Quản lý Bán Sách Thông Minh (Laravel 12 &bull; PHP 8 &bull; Bootstrap 5)
        </footer>
    </div>

    <!-- Smart Command Palette Modal (Ctrl + K) -->
    <div class="modal fade" id="quickSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" id="globalSearchInput" class="form-control border-0 shadow-none" placeholder="Nhập tên sách, mã ISBN, tên hoặc SĐT khách hàng, mã đơn BK-..." autocomplete="off">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2" id="globalSearchResults">
                    <div class="text-center text-secondary py-4 small">
                        <i class="bi bi-command fs-3 d-block mb-2 text-primary"></i>
                        Gõ từ khóa bất kỳ để tìm kiếm thông minh trên toàn hệ thống
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            // Theme switcher (Dark / Light mode)
            const htmlEl = document.documentElement;
            const themeBtn = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const savedTheme = localStorage.getItem('bv_theme') || 'light';

            function applyTheme(theme) {
                htmlEl.setAttribute('data-bs-theme', theme);
                themeIcon.className = theme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
                localStorage.setItem('bv_theme', theme);
            }
            applyTheme(savedTheme);

            themeBtn?.addEventListener('click', () => {
                applyTheme(htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
            });

            // Mobile sidebar toggle
            document.getElementById('sidebarToggle')?.addEventListener('click', () => {
                document.getElementById('bvSidebar')?.classList.toggle('show');
            });

            // Ctrl+K shortcut for Smart Command Palette
            const searchModalEl = document.getElementById('quickSearchModal');
            const searchInput = document.getElementById('globalSearchInput');
            const resultsBox = document.getElementById('globalSearchResults');

            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    const modal = bootstrap.Modal.getOrCreateInstance(searchModalEl);
                    modal.show();
                }
            });

            searchModalEl?.addEventListener('shown.bs.modal', () => searchInput?.focus());

            let timer = null;
            searchInput?.addEventListener('input', function () {
                clearTimeout(timer);
                const q = this.value.trim();
                if (!q) {
                    resultsBox.innerHTML = '<div class="text-center text-secondary py-4 small">Gõ từ khóa để bắt đầu tìm kiếm...</div>';
                    return;
                }
                timer = setTimeout(() => {
                    $.getJSON('{{ route("api.quick-search") }}', { q }, function (data) {
                        let html = '';
                        if (data.books?.length) {
                            html += '<div class="fw-bold text-uppercase small text-secondary px-2 mt-2 mb-1">Sách trong kho</div><div class="list-group list-group-flush mb-3">';
                            data.books.forEach(b => {
                                html += `<a href="${b.url}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3">
                                    <div>
                                        <div class="fw-semibold">${b.title}</div>
                                        <small class="text-secondary">ISBN: ${b.isbn} &bull; ${b.author || ''}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">${b.price}</div>
                                        <span class="badge bg-secondary-subtle text-secondary">Tồn: ${b.stock}</span>
                                    </div>
                                </a>`;
                            });
                            html += '</div>';
                        }
                        if (data.customers?.length) {
                            html += '<div class="fw-bold text-uppercase small text-secondary px-2 mb-1">Khách hàng thành viên</div><div class="list-group list-group-flush mb-3">';
                            data.customers.forEach(c => {
                                html += `<a href="${c.url}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3">
                                    <div><span class="fw-semibold">${c.name}</span> <small class="text-secondary">(${c.phone})</small></div>
                                    <span class="badge bg-info-subtle text-info">Hạng ${c.tier}</span>
                                </a>`;
                            });
                            html += '</div>';
                        }
                        if (data.orders?.length) {
                            html += '<div class="fw-bold text-uppercase small text-secondary px-2 mb-1">Đơn hàng</div><div class="list-group list-group-flush">';
                            data.orders.forEach(o => {
                                html += `<a href="${o.url}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3">
                                    <div><span class="fw-semibold">${o.order_code}</span> &bull; <small class="text-secondary">${o.customer}</small></div>
                                    <span class="fw-bold text-success">${o.total}</span>
                                </a>`;
                            });
                            html += '</div>';
                        }
                        if (!html) {
                            html = '<div class="text-center text-secondary py-4">Không tìm thấy kết quả phù hợp.</div>';
                        }
                        resultsBox.innerHTML = html;
                    });
                }, 180);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
