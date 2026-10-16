@extends('shop.layout')

@section('title', 'Giỏ Sách & Đặt Hàng Trực Tuyến - BookVerse.vn')

@section('content')
<!-- Thanh tiến trình đặt sách 3 bước -->
<div class="shop-card p-3 mb-4">
    <div class="row text-center small g-2 align-items-center">
        <div class="col-4">
            <span class="badge rounded-pill bg-success me-1">1</span>
            <strong class="text-success">Chọn sách vào giỏ</strong>
        </div>
        <div class="col-4">
            <span class="badge rounded-pill bg-danger me-1">2</span>
            <strong class="text-danger">Kiểm tra VIP &amp; Thanh toán</strong>
        </div>
        <div class="col-4 text-secondary">
            <span class="badge rounded-pill bg-secondary me-1">3</span>
            <span>Hoàn tất &amp; Nhận mã vận đơn</span>
        </div>
    </div>
</div>

@if (empty($cartItems))
    <div class="shop-card p-5 text-center">
        <i class="bi bi-bag-x fs-1 text-secondary d-block mb-2"></i>
        <h5 class="font-serif-book fw-bold">Giỏ sách của bạn đang trống</h5>
        <p class="text-secondary small mb-4">Hãy khám phá hàng trăm đầu sách công nghệ, kinh tế và văn học chính hãng tại BookVerse.vn.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-bv-primary rounded-3 px-4 py-2">
            <i class="bi bi-compass me-1"></i> Tiếp tục chọn sách
        </a>
    </div>
@else
    <div class="row g-4">
        <!-- Cột trái: Danh sách sách trong giỏ -->
        <div class="col-12 col-lg-7">
            <div class="shop-card p-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="font-serif-book fw-bold mb-0">
                        Giỏ sách của bạn ({{ count($cartItems) }} đầu sách)
                    </h5>
                    <a href="{{ route('shop.index') }}" class="small text-danger fw-semibold text-decoration-none">
                        <i class="bi bi-plus-circle me-1"></i> Chọn thêm sách
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Ấn phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                @php $b = $item['book']; @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="{{ route('shop.book', $b) }}" class="text-decoration-none flex-shrink-0">
                                                <div class="book-3d" style="width:52px;height:74px;background:{{ $b->cover_gradient }};padding:5px 5px 5px 9px;">
                                                    <div style="font-size:0.44rem;font-weight:700;line-height:1.15;overflow:hidden;">{{ Str::limit($b->title, 18) }}</div>
                                                    <div style="font-size:0.38rem;opacity:0.85;">{{ Str::limit($b->author?->name, 12) }}</div>
                                                </div>
                                            </a>
                                            <div>
                                                <a href="{{ route('shop.book', $b) }}" class="fw-bold text-decoration-none text-dark d-block">{{ $b->title }}</a>
                                                <div class="small text-secondary">{{ $b->author?->name }} &bull; {{ $b->publisher ?: 'NXB Tri Thức' }}</div>
                                                <div class="mt-1">
                                                    <span class="fw-bold text-danger small">{{ number_format($b->price, 0, ',', '.') }} đ</span>
                                                    <span class="price-old ms-1">{{ number_format($b->original_price, 0, ',', '.') }} đ</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center" style="width: 130px;">
                                        <form action="{{ route('shop.cart.update') }}" method="POST" class="d-inline-flex align-items-center">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $b->id }}">
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $b->stock }}"
                                                   class="form-control form-control-sm text-center fw-bold" style="width:68px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($item['line_total'], 0, ',', '.') }} đ</td>
                                    <td class="text-end">
                                        <form action="{{ route('shop.cart.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $b->id }}">
                                            <input type="hidden" name="quantity" value="0">
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Xóa khỏi giỏ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin nhận hàng & Tự động tra cứu Hội viên VIP -->
        <div class="col-12 col-lg-5">
            <div class="shop-card p-4">
                <h5 class="font-serif-book fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-person-vcard text-danger me-1"></i> Thông tin Giao sách &amp; Ưu đãi VIP
                </h5>

                <form action="{{ route('shop.checkout') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Số điện thoại nhận hàng &amp; tra cứu thẻ VIP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone-fill text-danger"></i></span>
                            <input type="text" name="phone" id="checkoutPhone" class="form-control" required
                                   placeholder="Nhập SĐT (VD: 0901234567)" value="{{ old('phone') }}">
                            <button type="button" class="btn btn-bv-outline" id="checkVipBtn">Kiểm tra VIP</button>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mt-1" style="font-size: 0.72rem;">
                            <span class="text-secondary">Thử nhanh SĐT mẫu:</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-danger text-decoration-none fw-semibold"
                                    onclick="document.getElementById('checkoutPhone').value='0901234567';document.getElementById('checkVipBtn').click();">
                                0901234567 (Kim Cương -10%)
                            </button>
                            <span class="text-muted">|</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-warning text-decoration-none fw-semibold"
                                    onclick="document.getElementById('checkoutPhone').value='0912345678';document.getElementById('checkVipBtn').click();">
                                0912345678 (Vàng -5%)
                            </button>
                        </div>
                        <div id="vipStatusAlert" class="small mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="checkoutName" class="form-control" required value="{{ old('name') }}" placeholder="Nhập họ tên người nhận sách">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email nhận hóa đơn điện tử</label>
                        <input type="email" name="email" id="checkoutEmail" class="form-control" value="{{ old('email') }}" placeholder="email@example.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Địa chỉ nhận sách chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="address" id="checkoutAddress" class="form-control" required value="{{ old('address') }}" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select">
                            <option value="qr_transfer">Chuyển khoản VietQR 24/7 (Xác nhận tức thì)</option>
                            <option value="cash">Thanh toán tiền mặt khi nhận sách (COD)</option>
                            <option value="card">Thẻ ATM nội địa / Visa / Mastercard</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="Ghi chú đơn hàng (VD: Bọc plastic sách, giao giờ hành chính...)">
                    </div>

                    <div class="p-3 rounded-3 mb-3" style="background: #faf8f5; border: 1px solid #e5e0d5;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-secondary">Tổng tiền sách tạm tính:</span>
                            <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1 text-danger">
                            <span>Chiết khấu Hội viên VIP (<span id="vipPercentText">0%</span>):</span>
                            <span class="fw-bold" id="vipDiscountText">-0 đ</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2 text-success">
                            <span>Phí đóng gói &amp; Vận chuyển:</span>
                            <span class="fw-semibold">Miễn phí (0 đ)</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold">Tổng thanh toán:</span>
                            <span class="fs-4 fw-bold text-danger" id="finalOnlineTotal">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-bv-primary w-100 py-2 rounded-3 shadow-sm">
                        <i class="bi bi-bag-check-fill me-1"></i> XÁC NHẬN ĐẶT MUA SÁCH
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    (function () {
        const subtotal = {{ (float) ($subtotal ?? 0) }};
        const phoneInput = document.getElementById('checkoutPhone');
        const checkBtn = document.getElementById('checkVipBtn');
        const alertEl = document.getElementById('vipStatusAlert');

        function formatVnd(n) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + ' đ';
        }

        function lookupVip() {
            const phone = (phoneInput?.value || '').trim();
            if (!phone) return;
            $.getJSON('{{ route("shop.lookup-customer") }}', { phone }, function (res) {
                if (res.found) {
                    document.getElementById('checkoutName').value = res.name || '';
                    document.getElementById('checkoutEmail').value = res.email || '';
                    if (res.address) document.getElementById('checkoutAddress').value = res.address;

                    const discount = Math.round(subtotal * (res.discount_percent / 100));
                    const finalTotal = Math.max(0, subtotal - discount);

                    document.getElementById('vipPercentText').textContent = res.discount_percent + '%';
                    document.getElementById('vipDiscountText').textContent = '-' + formatVnd(discount);
                    document.getElementById('finalOnlineTotal').textContent = formatVnd(finalTotal);

                    alertEl.innerHTML = `<div class="p-2 rounded-2 bg-success-subtle text-success border border-success-subtle"><i class="bi bi-stars me-1"></i>Hội viên <strong>${res.name}</strong> — <strong>Hạng ${res.tier_label}</strong> (Giảm trực tiếp <strong>${res.discount_percent}%</strong> &bull; Đang có ${res.points} điểm)</div>`;
                } else {
                    document.getElementById('vipPercentText').textContent = '0%';
                    document.getElementById('vipDiscountText').textContent = '-0 đ';
                    document.getElementById('finalOnlineTotal').textContent = formatVnd(subtotal);
                    alertEl.innerHTML = `<div class="p-2 rounded-2 bg-warning-subtle text-dark border border-warning-subtle"><i class="bi bi-person-plus-fill me-1"></i>Số điện thoại mới — Hệ thống sẽ tự động mở thẻ Hội viên và cộng điểm thưởng sau đơn hàng này.</div>`;
                }
            });
        }

        checkBtn?.addEventListener('click', lookupVip);
        phoneInput?.addEventListener('blur', lookupVip);
    })();
</script>
@endpush
