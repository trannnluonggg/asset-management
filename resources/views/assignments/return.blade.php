@extends('layouts.app')

@section('title', 'Thu hồi tài sản')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-undo me-2"></i>Thu hồi tài sản</h2>
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Thông tin cấp phát</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-laptop me-2"></i>Thông tin tài sản</h6>
                        <p class="mb-1"><strong>Mã tài sản:</strong> {{ $assignment->asset->asset_code }}</p>
                        <p class="mb-1"><strong>Tên tài sản:</strong> {{ $assignment->asset->asset_name }}</p>
                        <p class="mb-1"><strong>Danh mục:</strong> {{ $assignment->asset->category->name }}</p>
                        <p class="mb-0"><strong>Serial:</strong> {{ $assignment->asset->serial_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-user me-2"></i>Thông tin nhân viên</h6>
                        <p class="mb-1"><strong>Tên nhân viên:</strong> {{ $assignment->employee->full_name }}</p>
                        <p class="mb-1"><strong>Mã nhân viên:</strong> {{ $assignment->employee->employee_code }}</p>
                        <p class="mb-1"><strong>Bộ phận:</strong> {{ $assignment->employee->department->name }}</p>
                        <p class="mb-0"><strong>Email:</strong> {{ $assignment->employee->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Thông tin cấp phát</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Ngày cấp phát:</strong> {{ $assignment->assigned_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Ngày dự kiến thu hồi:</strong> {{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : 'Không xác định' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Trạng thái:</strong> 
                                    <span class="badge bg-light text-dark">{{ $assignment->status === 'active' ? 'Đang cấp phát' : $assignment->status }}</span>
                                </p>
                            </div>
                        </div>
                        @if($assignment->assignment_notes)
                            <p class="mb-0"><strong>Ghi chú cấp phát:</strong> {{ $assignment->assignment_notes }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('assignments.return', $assignment) }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="actual_return_date" class="form-label">Ngày thu hồi thực tế <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('actual_return_date') is-invalid @enderror" 
                               id="actual_return_date" name="actual_return_date" 
                               value="{{ old('actual_return_date', date('Y-m-d')) }}" required>
                        @error('actual_return_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="return_condition" class="form-label">Tình trạng tài sản khi thu hồi <span class="text-danger">*</span></label>
                        <select class="form-select @error('return_condition') is-invalid @enderror" 
                                id="return_condition" name="return_condition" required>
                            <option value="">Chọn tình trạng</option>
                            <option value="good" {{ old('return_condition') == 'good' ? 'selected' : '' }}>Tốt</option>
                            <option value="fair" {{ old('return_condition') == 'fair' ? 'selected' : '' }}>Khá</option>
                            <option value="poor" {{ old('return_condition') == 'poor' ? 'selected' : '' }}>Kém</option>
                            <option value="damaged" {{ old('return_condition') == 'damaged' ? 'selected' : '' }}>Hỏng</option>
                            <option value="lost" {{ old('return_condition') == 'lost' ? 'selected' : '' }}>Mất</option>
                        </select>
                        @error('return_condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="asset_status" class="form-label">Trạng thái tài sản sau khi thu hồi <span class="text-danger">*</span></label>
                <select class="form-select @error('asset_status') is-invalid @enderror" 
                        id="asset_status" name="asset_status" required>
                    <option value="">Chọn trạng thái</option>
                    <option value="available" {{ old('asset_status') == 'available' ? 'selected' : '' }}>Khả dụng</option>
                    <option value="maintenance" {{ old('asset_status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                    <option value="retired" {{ old('asset_status') == 'retired' ? 'selected' : '' }}>Ngừng sử dụng</option>
                </select>
                @error('asset_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="return_notes" class="form-label">Ghi chú thu hồi</label>
                <textarea class="form-control @error('return_notes') is-invalid @enderror" 
                          id="return_notes" name="return_notes" rows="4" 
                          placeholder="Ghi chú về tình trạng tài sản, lý do thu hồi, v.v...">{{ old('return_notes') }}</textarea>
                @error('return_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Lưu ý:</strong> Sau khi thu hồi, tài sản sẽ được cập nhật trạng thái và có thể được cấp phát lại cho nhân viên khác.
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-undo me-2"></i>Thu hồi tài sản
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const returnConditionSelect = document.getElementById('return_condition');
    const assetStatusSelect = document.getElementById('asset_status');
    const returnDateInput = document.getElementById('actual_return_date');
    
    // Set maximum date to today
    returnDateInput.max = new Date().toISOString().split('T')[0];
    
    // Auto-suggest asset status based on return condition
    returnConditionSelect.addEventListener('change', function() {
        const condition = this.value;
        const assetStatus = assetStatusSelect;
        
        // Clear current selection
        assetStatus.value = '';
        
        // Suggest status based on condition
        switch(condition) {
            case 'good':
            case 'fair':
                assetStatus.value = 'available';
                break;
            case 'poor':
            case 'damaged':
                assetStatus.value = 'maintenance';
                break;
            case 'lost':
                assetStatus.value = 'retired';
                break;
        }
    });
});
</script>
@endpush
@endsection