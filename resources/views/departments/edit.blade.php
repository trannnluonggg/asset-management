@extends('layouts.app')

@section('title', 'Chỉnh sửa bộ phận')
@section('page-title', 'Chỉnh sửa bộ phận')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa thông tin bộ phận
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('departments.update', $department) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Mã bộ phận <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $department->code) }}" required
                                       placeholder="VD: IT, HR, ACC">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên bộ phận <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $department->name) }}" required
                                       placeholder="VD: Công nghệ thông tin">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manager_id" class="form-label">Trưởng bộ phận</label>
                                <select class="form-select @error('manager_id') is-invalid @enderror" 
                                        id="manager_id" name="manager_id">
                                    <option value="">Chọn trưởng bộ phận</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                                {{ old('manager_id', $department->manager_id) == $employee->id ? 'selected' : '' }}
                                                data-department="{{ $employee->department->name ?? 'N/A' }}"
                                                data-position="{{ $employee->position ?? 'N/A' }}">
                                            {{ $employee->employee_code }} - {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="manager-info" class="mt-2" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body p-2">
                                            <small>
                                                <strong>Bộ phận hiện tại:</strong> <span id="manager-department"></span><br>
                                                <strong>Chức vụ:</strong> <span id="manager-position"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('is_active') is-invalid @enderror" 
                                        id="is_active" name="is_active" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="1" {{ old('is_active', $department->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ old('is_active', $department->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Mô tả về chức năng, nhiệm vụ của bộ phận...">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    @if($department->employees->count() > 0 && old('is_active') == '0')
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo:</strong> Bộ phận này có {{ $department->employees->count() }} nhân viên. 
                        Việc chuyển sang trạng thái không hoạt động có thể ảnh hưởng đến các chức năng khác.
                    </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Thay đổi trưởng bộ phận sẽ không ảnh hưởng đến bộ phận hiện tại của nhân viên được chọn.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-secondary">Hủy</a>
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
    const managerSelect = document.getElementById('manager_id');
    const managerInfo = document.getElementById('manager-info');
    const isActiveSelect = document.getElementById('is_active');
    
    // Function to show manager info
    function showManagerInfo() {
        const selectedOption = managerSelect.options[managerSelect.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('manager-department').textContent = selectedOption.dataset.department || 'N/A';
            document.getElementById('manager-position').textContent = selectedOption.dataset.position || 'N/A';
            managerInfo.style.display = 'block';
        } else {
            managerInfo.style.display = 'none';
        }
    }
    
    // Show manager info when selected
    managerSelect.addEventListener('change', showManagerInfo);
    
    // Show manager info if pre-selected
    if (managerSelect.value) {
        showManagerInfo();
    }
    
    // Check for employees when changing status
    isActiveSelect.addEventListener('change', function() {
        if (this.value === '0') {
            const employeeCount = {{ $department->employees->count() }};
            if (employeeCount > 0) {
                alert('Bộ phận này có ' + employeeCount + ' nhân viên. Việc chuyển sang trạng thái không hoạt động có thể ảnh hưởng đến các chức năng khác.');
            }
        }
    });
});
</script>
@endpush
@endsection