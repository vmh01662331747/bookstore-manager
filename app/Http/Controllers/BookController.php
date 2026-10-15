<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with(['category', 'author']);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($authorId = $request->query('author_id')) {
            $query->where('author_id', $authorId);
        }

        if ($stockStatus = $request->query('stock_status')) {
            if ($stockStatus === 'low') {
                $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
            } elseif ($stockStatus === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($stockStatus === 'in') {
                $query->whereColumn('stock', '>', 'min_stock');
            }
        }

        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'bestseller' => $query->orderByDesc('sold_count'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'stock_asc' => $query->orderBy('stock'),
            default => $query->latest(),
        };

        $books = $query->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('books.index', compact('books', 'categories', 'authors'));
    }

    public function create(): View
    {
        $book = new Book([
            'published_year' => (int) date('Y'),
            'pages' => 320,
            'stock' => 20,
            'min_stock' => 5,
            'rating' => 4.8,
            'cover_color' => '#4f46e5',
            'is_active' => true,
        ]);
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('books.form', compact('book', 'categories', 'authors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBook($request);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        $book = Book::create($data);

        return redirect()->route('books.show', $book)
            ->with('success', "Đã thêm sách \"{$book->title}\" vào hệ thống kho.");
    }

    public function show(Book $book): View
    {
        $book->load(['category', 'author', 'orderItems.order.customer']);

        $relatedBooks = Book::with('author')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('books.form', compact('book', 'categories', 'authors'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validateBook($request, $book->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        $book->update($data);

        return redirect()->route('books.show', $book)
            ->with('success', "Đã cập nhật thông tin sách \"{$book->title}\".");
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->orderItems()->exists()) {
            return back()->with('error', 'Không thể xóa sách đã phát sinh trong hóa đơn bán hàng. Bạn có thể ngừng kinh doanh sách này.');
        }

        $title = $book->title;
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', "Đã xóa sách \"{$title}\".");
    }

    /**
     * Nhập kho nhanh thông minh (1-click Smart Restock).
     */
    public function restock(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'added_quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $book->increment('stock', (int) $validated['added_quantity']);

        return back()->with('success', "Đã nhập thêm {$validated['added_quantity']} cuốn cho \"{$book->title}\". Tồn kho mới: {$book->fresh()->stock}.");
    }

    private function validateBook(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'isbn' => ['required', 'string', 'max:30', Rule::unique('books', 'isbn')->ignore($ignoreId)],
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'author_id' => ['required', 'exists:authors,id'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'published_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'pages' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:1'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'cover_color' => ['nullable', 'string', 'max:30'],
            'cover_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);
    }
}
