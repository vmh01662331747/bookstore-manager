@extends('layouts.app')

@section('title', ($category->exists ? 'Sửa Danh mục' : 'Thêm Danh mục') . ' - BookVerse AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">{{ $category->exists ? 'Chỉnh sửa Danh mục' : 'Thêm Danh mục Sách Mới' }}</h2>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary rounded-3">Quay lại</a>
</div>

<div class="bv-card p-4" style="max-width: 680px;">
    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required placeholder="VD: Công nghệ & AI">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Màu nhận diện</label>
                <select name="color" class="form-select">
                    @foreach (['primary' => 'Xanh Indigo', 'success' => 'Xanh Ngọc', 'warning' => 'Vàng Cam', 'info' => 'Xanh Dương', 'danger' => 'Đỏ Hồng'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('color', $category->color) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Biểu tượng (Bootstrap Icon)</label>
                <select name="icon" class="form-select">
                    @foreach (['bi-cpu' => 'Công nghệ (bi-cpu)', 'bi-graph-up-arrow' => 'Kinh tế (bi-graph-up-arrow)', 'bi-book-half' => 'Văn học (bi-book-half)', 'bi-lightbulb' => 'Kỹ năng (bi-lightbulb)', 'bi-rocket-takeoff' => 'Khoa học (bi-rocket-takeoff)', 'bi-journal-bookmark' => 'Sách chung'] as $icon => $label)
                        <option value="{{ $icon }}" @selected(old('icon', $category->icon) === $icon)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Mô tả</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
            <i class="bi bi-check2-circle me-1"></i> Lưu danh mục
        </button>
    </form>
</div>
@endsection
