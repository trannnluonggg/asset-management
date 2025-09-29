@extends('layouts.app')

@section('title', 'Chỉnh sửa nhân viên')
@section('page-title', 'Chỉnh sửa nhân viên')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa thông tin nhân viên
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_code" class="form-label">Mã nhân viên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('employee_code') is-invalid @enderror" 
                                       id="employee_code" name="employee_code" 
                                       value="{{ old('employee_code', $employee->employee_code) }}" required>
                                @error('employee_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                       id="full_name" name="full_name" 
                                       value="{{ old('full_name', $employee->full_name) }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $employee->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Bộ phận <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" name="department_id" required>
                                    <option value="">Chọn bộ phận</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">Chức vụ</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" 
                                       value="{{ old('position', $employee->position) }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hire_date" class="form-label">Ngày vào làm</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                       id="hire_date" name="hire_date" 
                                       value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}">
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Đang làm việc</option>
                                    <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    @if($employee->activeAssignments->count() > 0 && old('status') == 'inactive')
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo:</strong> Nhân viên này đang được cấp phát {{ $employee->activeAssignments->count() }} tài sản. 
                        Vui lòng thu hồi tất cả tài sản trước khi chuyển sang trạng thái không hoạt động.
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">Hủy</a>
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
    const statusSelect = document.getElementById('status');
    const hireDateInput = document.getElementById('hire_date');
    
    // Set maximum date to today for hire date
    hireDateInput.max = new Date().toISOString().split('T')[0];
    
    // Check for active assignments when changing status
    statusSelect.addEventListener('change', function() {
        if (this.value === 'inactive') {
            const activeAssignments = {{ $employee->activeAssignments->count() }};
            if (activeAssignments > 0) {
                alert('Nhân viên này đang được cấp phát ' + activeAssignments + ' tài sản. Vui lòng thu hồi tất cả tài sản trước khi chuyển sang trạng thái không hoạt động.');
            }
        }
    });
});
</script>
@endpush
@endsection