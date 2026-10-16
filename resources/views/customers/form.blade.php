@extends('layouts.app')

@section('title', ($customer->exists ? 'Sửa Khách hàng' : 'Thêm Khách hàng') . ' - BookVerse AI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">{{ $customer->exists ? 'Chỉnh sửa Hồ sơ Khách hàng' : 'Đăng ký Hội viên Mới' }}</h2>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary rounded-3">Quay lại</a>
</div>

<div class="bv-card p-4" style="max-width: 680px;">
    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if ($customer->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label fw-semibold">Họ và tên khách hàng <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Địa chỉ nhận sách</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address) }}">
        </div>

        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
            <i class="bi bi-check2-circle me-1"></i> Lưu thông tin hội viên
        </button>
    </form>
</div>
@endsection
