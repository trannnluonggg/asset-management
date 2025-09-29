@extends('layouts.app')

@section('title', 'Chỉnh sửa cấp phát tài sản')
@section('page-title', 'Chỉnh sửa cấp phát tài sản')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa thông tin cấp phát
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('assignments.update', $assignment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Asset Info (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Tài sản:</strong></label>
                            <div class="form-control-plaintext">
                                {{ $assignment->asset->asset_code }} - {{ $assignment->asset->asset_name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Danh mục:</strong></label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-info">{{ $assignment->asset->category->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Assignment Date (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Ngày cấp phát:</strong></label>
                            <div class="form-control-plaintext">
                                {{ $assignment->assigned_date->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Editable Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="employee_id" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" 
                                    id="employee_id" name="employee_id" required>
                                <option value="">Chọn nhân viên</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            {{ (old('employee_id', $assignment->employee_id) == $employee->id) ? 'selected' : '' }}
                                            data-department="{{ $employee->department->name }}">
                                        {{ $employee->employee_code }} - {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="employee-info" class="mt-2" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-body p-2">
                                        <small>
                                            <strong>Bộ phận:</strong> <span id="employee-department"></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="expected_return_date" class="form-label">Ngày trả dự kiến</label>
                            <input type="date" class="form-control @error('expected_return_date') is-invalid @enderror" 
                                   id="expected_return_date" name="expected_return_date" 
                                   value="{{ old('expected_return_date', $assignment->expected_return_date ? $assignment->expected_return_date->format('Y-m-d') : '') }}">
                            @error('expected_return_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('assignment_notes') is-invalid @enderror" 
                                  id="assignment_notes" name="assignment_notes" rows="3" 
                                  placeholder="Ghi chú về việc cấp phát tài sản...">{{ old('assignment_notes', $assignment->assignment_notes) }}</textarea>
                        @error('assignment_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Chỉ có thể thay đổi nhân viên được cấp phát, ngày trả dự kiến và ghi chú. Không thể thay đổi tài sản hoặc ngày cấp phát.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-secondary">Hủy</a>
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
    const employeeSelect = document.getElementById('employee_id');
    const employeeInfo = document.getElementById('employee-info');
    const expectedReturnDate = document.getElementById('expected_return_date');
    
    // Function to show employee info
    function showEmployeeInfo() {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('employee-department').textContent = selectedOption.dataset.department || 'N/A';
            employeeInfo.style.display = 'block';
        } else {
            employeeInfo.style.display = 'none';
        }
    }
    
    // Show employee info when selected
    employeeSelect.addEventListener('change', showEmployeeInfo);
    
    // Show employee info if pre-selected
    if (employeeSelect.value) {
        showEmployeeInfo();
    }
    
    // Set minimum date for expected return date to assignment date
    expectedReturnDate.min = '{{ $assignment->assigned_date->format('Y-m-d') }}';
});
</script>
@endpush
@endsection