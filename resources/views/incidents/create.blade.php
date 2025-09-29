@extends('layouts.app')

@section('title', 'Báo cáo sự cố mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-exclamation-triangle me-2"></i>Báo cáo sự cố mới</h2>
    <a href="{{ route('incidents.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('incidents.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Tài sản <span class="text-danger">*</span></label>
                        <select class="form-select @error('asset_id') is-invalid @enderror" 
                                id="asset_id" name="asset_id" required>
                            <option value="">Chọn tài sản</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" 
                                        {{ (old('asset_id') == $asset->id || (isset($selectedAssetId) && $selectedAssetId == $asset->id)) ? 'selected' : '' }}
                                        data-category="{{ $asset->category->name }}"
                                        data-employee="{{ $asset->currentAssignment->employee->full_name ?? 'N/A' }}"
                                        data-department="{{ $asset->currentAssignment->employee->department->name ?? 'N/A' }}">
                                    {{ $asset->asset_code }} - {{ $asset->asset_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="asset-info" class="mt-2" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <small>
                                        <strong>Danh mục:</strong> <span id="asset-category"></span><br>
                                        <strong>Người sử dụng:</strong> <span id="asset-employee"></span><br>
                                        <strong>Bộ phận:</strong> <span id="asset-department"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="incident_type" class="form-label">Loại sự cố <span class="text-danger">*</span></label>
                        <select class="form-select @error('incident_type') is-invalid @enderror" 
                                id="incident_type" name="incident_type" required>
                            <option value="">Chọn loại sự cố</option>
                            <option value="damage" {{ old('incident_type') == 'damage' ? 'selected' : '' }}>Hỏng hóc</option>
                            <option value="lost" {{ old('incident_type') == 'lost' ? 'selected' : '' }}>Mất</option>
                            <option value="theft" {{ old('incident_type') == 'theft' ? 'selected' : '' }}>Bị trộm</option>
                            <option value="malfunction" {{ old('incident_type') == 'malfunction' ? 'selected' : '' }}>Trục trặc</option>
                        </select>
                        @error('incident_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="incident_date" class="form-label">Ngày xảy ra sự cố <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('incident_date') is-invalid @enderror" 
                               id="incident_date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required>
                        @error('incident_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="5" required 
                          placeholder="Mô tả chi tiết về sự cố: nguyên nhân, tình trạng hiện tại, ảnh hưởng...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Lưu ý:</strong> Sau khi báo cáo sự cố, tài sản sẽ được chuyển sang trạng thái phù hợp (bảo trì hoặc ngừng sử dụng) và thông báo sẽ được gửi đến bộ phận quản lý.
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('incidents.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Báo cáo sự cố
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const assetSelect = document.getElementById('asset_id');
    const assetInfo = document.getElementById('asset-info');
    
    // Function to show asset info
    function showAssetInfo() {
        const selectedOption = assetSelect.options[assetSelect.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('asset-category').textContent = selectedOption.dataset.category || 'N/A';
            document.getElementById('asset-employee').textContent = selectedOption.dataset.employee || 'N/A';
            document.getElementById('asset-department').textContent = selectedOption.dataset.department || 'N/A';
            assetInfo.style.display = 'block';
        } else {
            assetInfo.style.display = 'none';
        }
    }
    
    // Show asset info when selected
    assetSelect.addEventListener('change', showAssetInfo);
    
    // Show asset info if pre-selected
    if (assetSelect.value) {
        showAssetInfo();
    }
    
    // Set maximum date to today
    const incidentDateInput = document.getElementById('incident_date');
    incidentDateInput.max = new Date().toISOString().split('T')[0];
});
</script>
@endpush
@endsection