@extends('layouts.app')

@section('title', 'Bán hàng Thông minh (Smart POS) - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Màn hình Bán hàng Thông minh (Smart POS)</h2>
        <p class="text-secondary small mb-0">Chọn sách nhanh bằng 1 click hoặc tìm theo mã ISBN, tự động tính chiết khấu VIP và điểm thưởng hội viên.</p>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-3">
        <i class="bi bi-receipt me-1"></i> Lịch sử hóa đơn
    </a>
</div>

<div class="row g-4">
    <!-- Cột trái: Danh mục & Kệ sách thông minh -->
    <div class="col-12 col-xl-7">
        <div class="bv-card p-3 mb-3">
            <div class="row g-2">
                <div class="col-12 col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="bi bi-upc-scan text-primary"></i></span>
                        <input type="text" id="posSearchInput" class="form-control" placeholder="Quét mã ISBN hoặc nhập tên sách, tác giả...">
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <select id="posCategoryFilter" class="form-select">
                        <option value="">Tất cả thể loại sách</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row g-3" id="posBookGrid">
            @foreach ($books as $book)
                <div class="col-12 col-md-6 pos-book-item"
                     data-id="{{ $book->id }}"
                     data-title="{{ $book->title }}"
                     data-isbn="{{ strtolower($book->isbn) }}"
                     data-author="{{ strtolower($book->author?->name) }}"
                     data-category="{{ $book->category_id }}"
                     data-price="{{ $book->price }}"
                     data-stock="{{ $book->stock }}">
                    <div class="bv-card bv-card-hover p-3 h-100 d-flex align-items-center justify-content-between gap-3 {{ $book->stock <= 0 ? 'opacity-50' : '' }}"
                         style="cursor: {{ $book->stock > 0 ? 'pointer' : 'not-allowed' }};"
                         onclick="window.posAddBook({{ $book->id }})">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bv-book-cover" style="background: {{ $book->cover_color }};">
                                <span>{{ Str::limit($book->title, 15) }}</span>
                                <small style="font-size:0.55rem;">{{ $book->stock }} kho</small>
                            </div>
                            <div>
                                <div class="fw-bold small">{{ Str::limit($book->title, 36) }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">
                                    {{ $book->author?->name }} &bull; <span class="font-monospace">{{ $book->isbn }}</span>
                                </div>
                                <div class="fw-bold text-primary mt-1">{{ number_format($book->price, 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-circle" @disabled($book->stock <= 0)>
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cột phải: Giỏ hàng & Thanh toán tự động -->
    <div class="col-12 col-xl-5">
        <div class="bv-card p-4 position-sticky" style="top: 86px;">
            <form method="POST" action="{{ route('orders.store') }}" id="posOrderForm">
                @csrf
                <h5 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bag-check-fill text-primary me-1"></i> Hóa đơn Thanh toán</span>
                    <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none p-0" id="clearCartBtn">Xóa giỏ</button>
                </h5>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Khách hàng / Hội viên VIP (Tự động giảm giá)</label>
                    <select name="customer_id" id="posCustomerSelect" class="form-select">
                        <option value="" data-discount="0" data-tier="Khách lẻ">-- Khách vãng lai (Không giảm giá) --</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" data-discount="{{ $c->discount_percent }}" data-tier="{{ $c->tier_label }}">
                                {{ $c->name }} ({{ $c->phone }}) — Hạng {{ $c->tier_label }} (-{{ $c->discount_percent }}%)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Kênh bán hàng</label>
                        <select name="channel" class="form-select form-select-sm">
                            <option value="pos">Tại quầy (POS)</option>
                            <option value="online">Đơn Online</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select form-select-sm">
                            <option value="cash">Tiền mặt</option>
                            <option value="qr_transfer">Chuyển khoản QR</option>
                            <option value="card">Thẻ ngân hàng</option>
                        </select>
                    </div>
                </div>

                <!-- Danh sách sách trong giỏ -->
                <div class="border rounded-3 p-2 mb-3" style="max-height: 290px; overflow-y: auto; border-color: var(--bv-border) !important;" id="posCartContainer">
                    <div class="text-center text-secondary py-4 small" id="posEmptyCart">
                        <i class="bi bi-cart3 fs-2 d-block mb-1"></i>
                        Chưa có sách nào trong hóa đơn. Hãy click chọn sách bên trái!
                    </div>
                    <div id="posCartRows"></div>
                </div>

                <div class="mb-3">
                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Ghi chú đơn hàng (VD: Gói quà tặng, giao giờ hành chính...)">
                </div>

                <!-- Bảng tổng hợp tiền & điểm thưởng thông minh -->
                <div class="p-3 rounded-4 bg-body-tertiary mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-secondary">Tạm tính (<span id="posTotalQty">0</span> cuốn):</span>
                        <span class="fw-semibold" id="posSubtotalText">0 đ</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1 text-danger">
                        <span>Chiết khấu VIP (<span id="posDiscountRateText">0%</span>):</span>
                        <span id="posDiscountText">-0 đ</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2 text-primary">
                        <span>Điểm thưởng cộng thêm:</span>
                        <span class="fw-semibold" id="posPointsText">+0 điểm</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold">Tổng thanh toán:</span>
                        <span class="fs-4 fw-bold text-success" id="posFinalTotalText">0 đ</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm" id="posSubmitBtn" disabled>
                    <i class="bi bi-check2-circle me-1"></i> Hoàn tất & Xuất Hóa Đơn
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const booksMap = {};
        document.querySelectorAll('.pos-book-item').forEach(el => {
            booksMap[el.dataset.id] = {
                id: parseInt(el.dataset.id),
                title: el.dataset.title,
                price: parseFloat(el.dataset.price),
                stock: parseInt(el.dataset.stock)
            };
        });

        let cart = {}; // { bookId: quantity }
        const cartRowsEl = document.getElementById('posCartRows');
        const emptyCartEl = document.getElementById('posEmptyCart');
        const customerSelect = document.getElementById('posCustomerSelect');
        const submitBtn = document.getElementById('posSubmitBtn');

        function formatVnd(n) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + ' đ';
        }

        window.posAddBook = function (bookId) {
            const book = booksMap[bookId];
            if (!book || book.stock <= 0) return;
            const currentQty = cart[bookId] || 0;
            if (currentQty + 1 > book.stock) {
                alert(`Sách "${book.title}" chỉ còn tối đa ${book.stock} cuốn trong kho!`);
                return;
            }
            cart[bookId] = currentQty + 1;
            renderCart();
        };

        window.posChangeQty = function (bookId, delta) {
            const book = booksMap[bookId];
            if (!book) return;
            const next = (cart[bookId] || 0) + delta;
            if (next <= 0) {
                delete cart[bookId];
            } else if (next <= book.stock) {
                cart[bookId] = next;
            }
            renderCart();
        };

        function renderCart() {
            const ids = Object.keys(cart);
            if (ids.length === 0) {
                emptyCartEl.style.display = 'block';
                cartRowsEl.innerHTML = '';
                submitBtn.disabled = true;
            } else {
                emptyCartEl.style.display = 'none';
                submitBtn.disabled = false;
                let html = '';
                ids.forEach((id, idx) => {
                    const b = booksMap[id];
                    const qty = cart[id];
                    const line = b.price * qty;
                    html += `
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <input type="hidden" name="items[${idx}][book_id]" value="${b.id}">
                        <input type="hidden" name="items[${idx}][quantity]" value="${qty}">
                        <div class="pe-2" style="max-width: 55%;">
                            <div class="fw-semibold text-truncate">${b.title}</div>
                            <span class="text-secondary">${formatVnd(b.price)}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="window.posChangeQty(${b.id}, -1)">-</button>
                            <span class="fw-bold px-2">${qty}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="window.posChangeQty(${b.id}, 1)">+</button>
                        </div>
                        <div class="fw-bold text-end" style="min-width: 85px;">${formatVnd(line)}</div>
                    </div>`;
                });
                cartRowsEl.innerHTML = html;
            }

            let subtotal = 0;
            let totalQty = 0;
            ids.forEach(id => {
                subtotal += booksMap[id].price * cart[id];
                totalQty += cart[id];
            });

            const selectedOpt = customerSelect.options[customerSelect.selectedIndex];
            const discountRate = parseFloat(selectedOpt?.dataset.discount || 0);
            const discountAmount = Math.round(subtotal * (discountRate / 100));
            const finalTotal = Math.max(0, subtotal - discountAmount);
            const points = Math.floor(finalTotal / 10000);

            document.getElementById('posTotalQty').textContent = totalQty;
            document.getElementById('posSubtotalText').textContent = formatVnd(subtotal);
            document.getElementById('posDiscountRateText').textContent = discountRate + '%';
            document.getElementById('posDiscountText').textContent = '-' + formatVnd(discountAmount);
            document.getElementById('posPointsText').textContent = '+' + points + ' điểm';
            document.getElementById('posFinalTotalText').textContent = formatVnd(finalTotal);
        }

        customerSelect?.addEventListener('change', renderCart);
        document.getElementById('clearCartBtn')?.addEventListener('click', () => {
            cart = {};
            renderCart();
        });

        // Lọc sách tức thời trên màn hình POS
        const searchInput = document.getElementById('posSearchInput');
        const catFilter = document.getElementById('posCategoryFilter');

        function filterBooks() {
            const q = (searchInput.value || '').toLowerCase().trim();
            const cat = catFilter.value;
            document.querySelectorAll('.pos-book-item').forEach(el => {
                const matchText = !q || el.dataset.title.toLowerCase().includes(q) || el.dataset.isbn.includes(q) || el.dataset.author.includes(q);
                const matchCat = !cat || el.dataset.category === cat;
                el.style.display = (matchText && matchCat) ? '' : 'none';
            });
        }
        searchInput?.addEventListener('input', filterBooks);
        catFilter?.addEventListener('change', filterBooks);

        // Nếu có preselectedBookId từ trang chi tiết sách
        const preselected = @json($preselectedBookId);
        if (preselected && booksMap[preselected]) {
            window.posAddBook(preselected);
        }
    })();
</script>
@endpush
