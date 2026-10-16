@extends('layouts.app')

@section('title', 'Quản lý Tác giả - BookVerse AI')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Danh sách Tác giả</h2>
        <p class="text-secondary small mb-0">Quản lý hồ sơ tác giả, tiểu sử và tổng doanh số phát hành sách.</p>
    </div>
    <a href="{{ route('authors.create') }}" class="btn btn-primary rounded-3 fw-semibold">
        <i class="bi bi-person-plus-fill me-1"></i> Thêm tác giả
    </a>
</div>

<div class="row g-4">
    @foreach ($authors as $author)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="bv-card bv-card-hover p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-primary text-white fw-bold d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            {{ $author->initials }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $author->name }}</h5>
                            <small class="text-secondary">{{ $author->nationality ?: 'Quốc tế' }} {{ $author->birth_year ? '• ' . $author->birth_year : '' }}</small>
                        </div>
                    </div>
                    <p class="small text-secondary mb-3">{{ $author->bio ?: 'Chưa có thông tin tiểu sử.' }}</p>
                </div>

                <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--bv-border) !important;">
                    <div class="small">
                        <span class="badge bg-primary-subtle text-primary">{{ $author->books_count }} tác phẩm</span>
                        <span class="badge bg-success-subtle text-success ms-1">Đã bán: {{ (int) $author->total_sold }}</span>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('books.index', ['author_id' => $author->id]) }}" class="btn btn-outline-primary" title="Xem sách của tác giả">
                            <i class="bi bi-journal-bookmark"></i>
                        </a>
                        <a href="{{ route('authors.edit', $author) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('authors.destroy', $author) }}" method="POST" onsubmit="return confirm('Xóa tác giả này?');">
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
