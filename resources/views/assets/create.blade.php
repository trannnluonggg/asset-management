@extends('layouts.app')

@section('title', 'Thêm tài sản mới - Livespo Asset Management')
@section('page-title', 'Thêm tài sản mới')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus me-2"></i>
                    Thông tin tài sản
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="asset_code" class="form-label">Mã tài sản <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('asset_code') is-invalid @enderror" 
                                       id="asset_code" 
                                       name="asset_code" 
                                       value="{{ old('asset_code') }}" 
                                       required>
                                @error('asset_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="asset_name" class="form-label">Tên tài sản <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('asset_name') is-invalid @enderror" 
                               id="asset_name" 
                               name="asset_name" 
                               value="{{ old('asset_name') }}" 
                               required>
                        @error('asset_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="brand" class="form-label">Thương hiệu</label>
                                <input type="text" 
                                       class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" 
                                       name="brand" 
                                       value="{{ old('brand') }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" 
                                       class="form-control @error('model') is-invalid @enderror" 
                                       id="model" 
                                       name="model" 
                                       value="{{ old('model') }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="serial_number" class="form-label">Số serial</label>
                        <input type="text" 
                               class="form-control @error('serial_number') is-invalid @enderror" 
                               id="serial_number" 
                               name="serial_number" 
                               value="{{ old('serial_number') }}">
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purchase_date" class="form-label">Ngày mua</label>
                                <input type="date" 
                                       class="form-control @error('purchase_date') is-invalid @enderror" 
                                       id="purchase_date" 
                                       name="purchase_date" 
                                       value="{{ old('purchase_date') }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purchase_price" class="form-label">Giá mua (VNĐ)</label>
                                <input type="number" 
                                       class="form-control @error('purchase_price') is-invalid @enderror" 
                                       id="purchase_price" 
                                       name="purchase_price" 
                                       value="{{ old('purchase_price') }}" 
                                       min="0" 
                                       step="1000">
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="warranty_expiry" class="form-label">Hết hạn bảo hành</label>
                                <input type="date" 
                                       class="form-control @error('warranty_expiry') is-invalid @enderror" 
                                       id="warranty_expiry" 
                                       name="warranty_expiry" 
                                       value="{{ old('warranty_expiry') }}">
                                @error('warranty_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="condition_status" class="form-label">Tình trạng</label>
                                <select class="form-control @error('condition_status') is-invalid @enderror" 
                                        id="condition_status" 
                                        name="condition_status">
                                    <option value="new" {{ old('condition_status') == 'new' ? 'selected' : '' }}>Mới</option>
                                    <option value="good" {{ old('condition_status') == 'good' ? 'selected' : '' }}>Tốt</option>
                                    <option value="fair" {{ old('condition_status') == 'fair' ? 'selected' : '' }}>Khá</option>
                                    <option value="poor" {{ old('condition_status') == 'poor' ? 'selected' : '' }}>Kém</option>
                                </select>
                                @error('condition_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="location" class="form-label">Vị trí</label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       id="location" 
                                       name="location" 
                                       value="{{ old('location') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status">
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Khả dụng</option>
                                    <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Đã giao</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                                    <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Ngừng sử dụng</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Mã QR sẽ được tự động tạo sau khi lưu tài sản.
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu tài sản
                        </button>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    Hướng dẫn
                </h6>
            </div>
            <div class="card-body">
                <h6>Thông tin bắt buộc:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>Mã tài sản</li>
                    <li><i class="fas fa-check text-success me-2"></i>Tên tài sản</li>
                    <li><i class="fas fa-check text-success me-2"></i>Danh mục</li>
                </ul>
                
                <hr>
                
                <h6>Lưu ý:</h6>
                <ul class="list-unstyled text-muted">
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>Mã tài sản phải duy nhất</li>
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>QR code sẽ tự động tạo</li>
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>Có thể cập nhật thông tin sau</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection