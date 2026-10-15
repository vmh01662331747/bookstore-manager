<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Author::withCount('books')
            ->withSum('books as total_sold', 'sold_count');

        if ($q = trim((string) $request->query('q', ''))) {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('nationality', 'like', "%{$q}%");
        }

        $authors = $query->orderByDesc('total_sold')->get();

        return view('authors.index', compact('authors'));
    }

    public function create(): View
    {
        $author = new Author();
        return view('authors.form', compact('author'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'birth_year' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        Author::create($data);

        return redirect()->route('authors.index')
            ->with('success', "Đã thêm tác giả \"{$data['name']}\".");
    }

    public function edit(Author $author): View
    {
        return view('authors.form', compact('author'));
    }

    public function update(Request $request, Author $author): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'birth_year' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $author->update($data);

        return redirect()->route('authors.index')
            ->with('success', "Đã cập nhật tác giả \"{$author->name}\".");
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Không thể xóa tác giả đang có sách trong hệ thống.');
        }

        $author->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Đã xóa tác giả.');
    }
}
