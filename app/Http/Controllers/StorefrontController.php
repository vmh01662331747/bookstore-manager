<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    /**
     * Trang chủ Website Nhà sách Online dành cho Khách hàng.
     */
    public function index(Request $request): View
    {
        $featuredBooks = Book::with(['category', 'author'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->where('is_featured', true)
            ->orderByDesc('sold_count')
            ->take(4)
            ->get();

        $query = Book::with(['category', 'author'])->where('is_active', true);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($authorId = $request->query('author_id')) {
            $query->where('author_id', $authorId);
        }

        $sort = $request->query('sort', 'popular');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('sold_count'),
        };

        $books = $query->paginate(8)->withQueryString();
        $categories = Category::withCount('books')->orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('shop.index', compact('featuredBooks', 'books', 'categories', 'authors'));
    }

    /**
     * Trang chi tiết sách cho Khách hàng.
     */
    public function show(Book $book): View
    {
        $book->load(['category', 'author']);

        $relatedBooks = Book::with(['category', 'author'])
            ->where('is_active', true)
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('book', 'relatedBooks'));
    }

    /**
     * Xem giỏ hàng & trang Thanh toán Online.
     */
    public function cart(): View
    {
        $cart = session('shop_cart', []);
        $bookIds = array_keys($cart);
        $books = Book::with(['author', 'category'])->whereIn('id', $bookIds)->get()->keyBy('id');

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $bookId => $qty) {
            if (isset($books[$bookId])) {
                /** @var Book $b */
                $b = $books[$bookId];
                $validQty = min($qty, max(1, $b->stock));
                $lineTotal = $b->price * $validQty;
                $subtotal += $lineTotal;
                $cartItems[] = [
                    'book' => $b,
                    'quantity' => $validQty,
                    'line_total' => $lineTotal,
                ];
            }
        }

        return view('shop.cart', compact('cartItems', 'subtotal'));
    }

    /**
     * Thêm sách vào giỏ hàng Online (Session).
     */
    public function addToCart(Request $request, Book $book): RedirectResponse
    {
        if (!$book->is_active || $book->stock <= 0) {
            return back()->with('error', 'Sách này hiện đang tạm hết hàng.');
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = session('shop_cart', []);
        $current = $cart[$book->id] ?? 0;

        if ($current + $qty > $book->stock) {
            return back()->with('error', "Kho chỉ còn {$book->stock} cuốn \"{$book->title}\".");
        }

        $cart[$book->id] = $current + $qty;
        session(['shop_cart' => $cart]);

        if ($request->boolean('buy_now')) {
            return redirect()->route('shop.cart')
                ->with('success', "Đã thêm \"{$book->title}\" vào giỏ hàng. Mời bạn tiến hành đặt hàng!");
        }

        return back()->with('success', "Đã thêm \"{$book->title}\" vào giỏ mua sắm!");
    }

    /**
     * Cập nhật số lượng hoặc xóa sách khỏi giỏ hàng Online.
     */
    public function updateCart(Request $request): RedirectResponse
    {
        $bookId = (int) $request->input('book_id');
        $qty = (int) $request->input('quantity', 0);
        $cart = session('shop_cart', []);

        if ($qty <= 0) {
            unset($cart[$bookId]);
        } else {
            $book = Book::find($bookId);
            if ($book) {
                $cart[$bookId] = min($qty, $book->stock);
            }
        }

        session(['shop_cart' => $cart]);

        return redirect()->route('shop.cart')->with('success', 'Đã cập nhật giỏ hàng.');
    }

    /**
     * API kiểm tra số điện thoại khách hàng để tự động áp dụng ưu đãi VIP khi mua Online.
     */
    public function lookupCustomer(Request $request): JsonResponse
    {
        $phone = trim((string) $request->query('phone', ''));
        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'name' => $customer->name,
            'email' => $customer->email,
            'address' => $customer->address,
            'tier_label' => $customer->tier_label,
            'discount_percent' => $customer->discount_percent,
            'points' => $customer->points,
        ]);
    }

    /**
     * Đặt hàng trực tuyến (Online Checkout) -> Tự động lưu vào CSDL XAMPP MySQL & đồng bộ Admin.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $cart = session('shop_cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cash,qr_transfer,card'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $cart) {
                // Tìm hoặc tạo mới khách hàng hội viên theo SĐT
                $customer = Customer::firstOrNew(['phone' => $validated['phone']]);
                $customer->name = $validated['name'];
                $customer->email = $validated['email'] ?? $customer->email;
                $customer->address = $validated['address'];
                if (!$customer->exists) {
                    $customer->tier = 'bronze';
                    $customer->points = 0;
                    $customer->total_spent = 0;
                }
                $customer->save();

                $orderCode = 'BK-' . now()->format('ymd') . '-' . Str::upper(Str::random(4));

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_id' => $customer->id,
                    'channel' => 'online',
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'note' => 'Giao tới: ' . $validated['address'] . ($validated['note'] ? ' | Ghi chú: ' . $validated['note'] : ''),
                ]);

                foreach ($cart as $bookId => $qty) {
                    /** @var Book $book */
                    $book = Book::lockForUpdate()->findOrFail($bookId);
                    if ($book->stock < $qty) {
                        throw new \RuntimeException("Sách \"{$book->title}\" chỉ còn {$book->stock} cuốn.");
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'book_id' => $book->id,
                        'quantity' => $qty,
                        'unit_price' => $book->price,
                        'cost_price' => $book->cost_price,
                        'line_total' => $book->price * $qty,
                    ]);

                    $book->decrement('stock', $qty);
                    $book->increment('sold_count', $qty);
                }

                $order->load('customer');
                $order->recalculateTotals();

                // Cộng điểm thưởng và nâng hạng tự động cho khách đặt Online
                $customer->total_spent += $order->total_amount;
                $customer->points += (int) floor($order->total_amount / 10000);
                $customer->recalculateTier();
                $customer->save();

                return $order;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        session()->forget('shop_cart');

        return redirect()->route('shop.track', ['code' => $order->order_code])
            ->with('success', "Đặt hàng Online thành công! Mã đơn của bạn là {$order->order_code}.");
    }

    /**
     * Trang Tra cứu Đơn hàng Online & Xác nhận đặt hàng.
     */
    public function trackOrder(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));
        $phone = trim((string) $request->query('phone', ''));

        $order = null;
        $customerOrders = collect();

        if ($code !== '') {
            $order = Order::with(['customer', 'items.book.author'])->where('order_code', $code)->first();
        }

        if ($phone !== '') {
            $customerOrders = Order::with(['customer', 'items.book'])
                ->whereHas('customer', fn ($q) => $q->where('phone', $phone))
                ->latest()
                ->get();
        }

        return view('shop.track', compact('order', 'customerOrders', 'code', 'phone'));
    }
}
