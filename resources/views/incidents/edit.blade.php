@extends('layouts.app')

@section('title', 'Chỉnh sửa báo cáo sự cố')
@section('page-title', 'Chỉnh sửa báo cáo sự cố')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa thông tin sự cố
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('incidents.update', $incident) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Asset Info (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Tài sản:</strong></label>
                            <div class="form-control-plaintext">
                                {{ $incident->asset->asset_code }} - {{ $incident->asset->asset_name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Danh mục:</strong></label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-info">{{ $incident->asset->category->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Editable Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="incident_type" class="form-label">Loại sự cố <span class="text-danger">*</span></label>
                            <select class="form-select @error('incident_type') is-invalid @enderror" 
                                    id="incident_type" name="incident_type" required>
                                <option value="">Chọn loại sự cố</option>
                                <option value="damage" {{ old('incident_type', $incident->incident_type) == 'damage' ? 'selected' : '' }}>Hỏng hóc</option>
                                <option value="lost" {{ old('incident_type', $incident->incident_type) == 'lost' ? 'selected' : '' }}>Mất</option>
                                <option value="theft" {{ old('incident_type', $incident->incident_type) == 'theft' ? 'selected' : '' }}>Bị trộm</option>
                                <option value="malfunction" {{ old('incident_type', $incident->incident_type) == 'malfunction' ? 'selected' : '' }}>Trục trặc</option>
                            </select>
                            @error('incident_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="incident_date" class="form-label">Ngày xảy ra sự cố <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('incident_date') is-invalid @enderror" 
                                   id="incident_date" name="incident_date" 
                                   value="{{ old('incident_date', $incident->incident_date->format('Y-m-d')) }}" required>
                            @error('incident_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">Chọn trạng thái</option>
                                <option value="pending" {{ old('status', $incident->status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="investigating" {{ old('status', $incident->status) == 'investigating' ? 'selected' : '' }}>Đang điều tra</option>
                                <option value="resolved" {{ old('status', $incident->status) == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="closed" {{ old('status', $incident->status) == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="5" required 
                                  placeholder="Mô tả chi tiết về sự cố: nguyên nhân, tình trạng hiện tại, ảnh hưởng...">{{ old('description', $incident->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="resolution" class="form-label">Giải pháp</label>
                        <textarea class="form-control @error('resolution') is-invalid @enderror" 
                                  id="resolution" name="resolution" rows="4" 
                                  placeholder="Mô tả cách giải quyết sự cố (nếu có)...">{{ old('resolution', $incident->resolution) }}</textarea>
                        @error('resolution')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Khi thay đổi trạng thái thành "Đã giải quyết" hoặc "Đã đóng", hệ thống sẽ tự động cập nhật thông tin người giải quyết và thời gian.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const incidentDateInput = document.getElementById('incident_date');
    const statusSelect = document.getElementById('status');
    const resolutionTextarea = document.getElementById('resolution');
    
    // Set maximum date to today
    incidentDateInput.max = new Date().toISOString().split('T')[0];
    
    // Show/hide resolution field based on status
    function toggleResolutionField() {
        const status = statusSelect.value;
        const resolutionGroup = resolutionTextarea.closest('.mb-3');
        
        if (status === 'resolved' || status === 'closed') {
            resolutionGroup.style.display = 'block';
            resolutionTextarea.required = true;
        } else {
            resolutionGroup.style.display = 'block'; // Always show for editing
            resolutionTextarea.required = false;
        }
    }
    
    statusSelect.addEventListener('change', toggleResolutionField);
    toggleResolutionField(); // Initial call
});
</script>
@endpush
@endsection