<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('books')
            ->withSum('books as total_stock', 'stock')
            ->withSum('books as total_sold', 'sold_count')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $category = new Category([
            'color' => 'primary',
            'icon' => 'bi-journal-bookmark',
        ]);

        return view('categories.form', compact('category'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'color' => ['required', 'string', 'max:30'],
            'icon' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::lower(Str::random(3));

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', "Đã tạo danh mục \"{$data['name']}\".");
    }

    public function edit(Category $category): View
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($category->id)],
            'color' => ['required', 'string', 'max:30'],
            'icon' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', "Đã cập nhật danh mục \"{$category->name}\".");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục đang chứa sách. Hãy chuyển sách sang danh mục khác trước.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Đã xóa danh mục thành công.');
    }
}
