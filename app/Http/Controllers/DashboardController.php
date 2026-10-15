<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $completedOrders = Order::where('status', 'completed');

        $totalRevenue = (float) (clone $completedOrders)->sum('total_amount');
        $todayRevenue = (float) (clone $completedOrders)->whereDate('created_at', Carbon::today())->sum('total_amount');
        $totalOrders = Order::count();
        $totalBooks = Book::count();
        $totalStock = (int) Book::sum('stock');
        $totalCustomers = Customer::count();

        // Ước tính lợi nhuận gộp từ các đơn hoàn tất
        $completedOrderIds = Order::where('status', 'completed')->pluck('id');
        $totalCost = (float) OrderItem::whereIn('order_id', $completedOrderIds)
            ->selectRaw('COALESCE(SUM(cost_price * quantity), 0) as total_cost')
            ->value('total_cost');
        $estimatedProfit = max(0, $totalRevenue - $totalCost);

        // Sách sắp hết hàng hoặc đã hết (Smart Restock Alerts)
        $lowStockBooks = Book::with(['category', 'author'])
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->orderByDesc('sold_count')
            ->get();

        // Top 5 sách bán chạy nhất
        $topBooks = Book::with(['category', 'author'])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        // Đơn hàng gần nhất
        $recentOrders = Order::with(['customer', 'items.book'])
            ->latest()
            ->take(6)
            ->get();

        // Biểu đồ doanh thu 7 ngày gần nhất
        $chartLabels = [];
        $chartRevenues = [];
        $chartOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $chartLabels[] = $day->format('d/m');
            $chartRevenues[] = (float) Order::where('status', 'completed')
                ->whereDate('created_at', $day)
                ->sum('total_amount');
            $chartOrders[] = Order::where('status', 'completed')
                ->whereDate('created_at', $day)
                ->count();
        }

        // Phân bổ theo danh mục
        $categoryStats = Category::withCount('books')
            ->withSum('books as total_stock', 'stock')
            ->withSum('books as total_sold', 'sold_count')
            ->get();

        return view('dashboard', compact(
            'totalRevenue',
            'todayRevenue',
            'estimatedProfit',
            'totalOrders',
            'totalBooks',
            'totalStock',
            'totalCustomers',
            'lowStockBooks',
            'topBooks',
            'recentOrders',
            'chartLabels',
            'chartRevenues',
            'chartOrders',
            'categoryStats'
        ));
    }

    /**
     * API tìm kiếm thông minh toàn cục (Smart Command Palette Ctrl+K).
     */
    public function quickSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['books' => [], 'customers' => [], 'orders' => []]);
        }

        $books = Book::with('author')
            ->where('title', 'like', "%{$q}%")
            ->orWhere('isbn', 'like', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn (Book $b) => [
                'id' => $b->id,
                'title' => $b->title,
                'isbn' => $b->isbn,
                'author' => $b->author?->name,
                'price' => number_format($b->price, 0, ',', '.') . ' đ',
                'stock' => $b->stock,
                'url' => route('books.show', $b),
            ]);

        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'tier' => $c->tier_label,
                'url' => route('customers.show', $c),
            ]);

        $orders = Order::with('customer')
            ->where('order_code', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'order_code' => $o->order_code,
                'customer' => $o->customer?->name ?? 'Khách lẻ',
                'total' => number_format($o->total_amount, 0, ',', '.') . ' đ',
                'url' => route('orders.show', $o),
            ]);

        return response()->json([
            'books' => $books,
            'customers' => $customers,
            'orders' => $orders,
        ]);
    }
}
