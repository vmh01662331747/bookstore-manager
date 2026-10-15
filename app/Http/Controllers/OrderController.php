<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'items.book']);

        if ($q = trim((string) $request->query('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('order_code', 'like', "%{$q}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($channel = $request->query('channel')) {
            $query->where('channel', $channel);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request): View
    {
        $books = Book::with(['category', 'author'])
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->get();
        $categories = Category::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $preselectedBookId = $request->query('book_id');

        return view('orders.create', compact('books', 'categories', 'customers', 'preselectedBookId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'channel' => ['required', 'in:pos,online'],
            'payment_method' => ['required', 'in:cash,qr_transfer,card'],
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'exists:books,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Gộp các dòng trùng book_id
        $groupedItems = [];
        foreach ($validated['items'] as $item) {
            $bookId = (int) $item['book_id'];
            $qty = (int) $item['quantity'];
            $groupedItems[$bookId] = ($groupedItems[$bookId] ?? 0) + $qty;
        }

        try {
            $order = DB::transaction(function () use ($validated, $groupedItems) {
                $orderCode = 'BK-' . now()->format('ymd') . '-' . Str::upper(Str::random(4));

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'channel' => $validated['channel'],
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'note' => $validated['note'] ?? null,
                ]);

                foreach ($groupedItems as $bookId => $qty) {
                    /** @var Book $book */
                    $book = Book::lockForUpdate()->findOrFail($bookId);

                    if ($book->stock < $qty) {
                        throw new \RuntimeException("Sách \"{$book->title}\" chỉ còn {$book->stock} cuốn trong kho (yêu cầu {$qty}).");
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

                // Tích điểm & tự động nâng hạng VIP cho khách hàng
                if ($order->customer) {
                    $customer = $order->customer;
                    $customer->total_spent += $order->total_amount;
                    $customer->points += (int) floor($order->total_amount / 10000);
                    $customer->recalculateTier();
                    $customer->save();
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)
            ->with('success', "Tạo hóa đơn {$order->order_code} thành công! Đã tự động trừ kho và tích điểm thành viên.");
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.book.author', 'items.book.category']);
        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã được hủy trước đó.');
        }

        DB::transaction(function () use ($order) {
            $order->load(['items.book', 'customer']);

            foreach ($order->items as $item) {
                if ($item->book) {
                    $item->book->increment('stock', $item->quantity);
                    $item->book->decrement('sold_count', min($item->book->sold_count, $item->quantity));
                }
            }

            if ($order->customer && $order->status === 'completed') {
                $customer = $order->customer;
                $customer->total_spent = max(0, $customer->total_spent - $order->total_amount);
                $customer->points = max(0, $customer->points - (int) floor($order->total_amount / 10000));
                $customer->recalculateTier();
                $customer->save();
            }

            $order->update(['status' => 'cancelled']);
        });

        return redirect()->route('orders.show', $order)
            ->with('success', "Đã hủy đơn hàng {$order->order_code} và hoàn trả số lượng sách về kho.");
    }
}
