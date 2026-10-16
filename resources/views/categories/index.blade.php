@extends('layouts.app')

@section('title', 'Danh mục Thể loại Sách - BookVerse AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Danh mục Thể loại Sách</h2>
        <p class="text-secondary small mb-0">Phân loại kho sách khoa học kèm thống kê số lượng đầu sách và doanh số theo từng lĩnh vực.</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary rounded-3 fw-semibold">
        <i class="bi bi-plus-lg me-1"></i> Thêm danh mục
    </a>
</div>

<div class="row g-4">
    @foreach ($categories as $cat)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="bv-card bv-card-hover p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="bv-kpi-icon bg-{{ $cat->color }} bg-opacity-10 text-{{ $cat->color }}">
                            <i class="bi {{ $cat->icon }}"></i>
                        </span>
                        <span class="badge bg-{{ $cat->color }}-subtle text-{{ $cat->color }} rounded-pill px-3 py-1">
                            {{ $cat->books_count }} đầu sách
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $cat->name }}</h5>
                    <p class="text-secondary small mb-3">{{ $cat->description ?: 'Chưa có mô tả.' }}</p>
                </div>

                <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--bv-border) !important;">
                    <div class="small text-secondary">
                        Tồn kho: <strong>{{ (int) $cat->total_stock }}</strong> &bull; Đã bán: <strong class="text-success">{{ (int) $cat->total_sold }}</strong>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('books.index', ['category_id' => $cat->id]) }}" class="btn btn-outline-primary" title="Xem sách">
                            <i class="bi bi-bookshelf"></i>
                        </a>
                        <a href="{{ route('categories.edit', $cat) }}" class="btn btn-outline-secondary" title="Sửa">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
