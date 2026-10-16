@extends('layouts.app')

@section('title', ($author->exists ? 'Sửa Tác giả' : 'Thêm Tác giả') . ' - BookVerse AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">{{ $author->exists ? 'Chỉnh sửa Tác giả' : 'Thêm Tác giả Mới' }}</h2>
    <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary rounded-3">Quay lại</a>
</div>

<div class="bv-card p-4" style="max-width: 680px;">
    <form method="POST" action="{{ $author->exists ? route('authors.update', $author) : route('authors.store') }}">
        @csrf
        @if ($author->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label fw-semibold">Họ và tên tác giả <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $author->name) }}" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Quốc tịch</label>
                <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $author->nationality) }}" placeholder="VD: Việt Nam, Hoa Kỳ">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Năm sinh</label>
                <input type="text" name="birth_year" class="form-control" value="{{ old('birth_year', $author->birth_year) }}" placeholder="VD: 1955">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Tiểu sử & Giải thưởng nổi bật</label>
            <textarea name="bio" rows="4" class="form-control">{{ old('bio', $author->bio) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
            <i class="bi bi-check2-circle me-1"></i> Lưu thông tin tác giả
        </button>
    </form>
</div>
@endsection
