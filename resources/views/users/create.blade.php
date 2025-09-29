@extends('layouts.app')

@section('title', 'Thêm người dùng mới')

@section('page-title', 'Thêm người dùng mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Thêm người dùng mới
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Tên đăng nhập phải là duy nhất trong hệ thống</div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự</div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Employee -->
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Nhân viên</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" 
                                id="employee_id" name="employee_id">
                            <option value="">-- Chọn nhân viên (tùy chọn) --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->employee_code }}
                                    @if($employee->department)
                                        ({{ $employee->department->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Liên kết tài khoản với thông tin nhân viên</div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            <option value="">-- Chọn vai trò --</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                Admin - Quản trị viên hệ thống
                            </option>
                            <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>
                                HR - Nhân sự
                            </option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>
                                User - Người dùng thông thường
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt tài khoản
                            </label>
                        </div>
                        <div class="form-text">Tài khoản được kích hoạt có thể đăng nhập vào hệ thống</div>
                    </div>

                    <!-- Role Descriptions -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Phân quyền vai trò:</h6>
                        <ul class="mb-0">
                            <li><strong>Admin:</strong> Toàn quyền quản lý hệ thống, bao gồm quản lý người dùng và cài đặt</li>
                            <li><strong>HR:</strong> Quản lý tài sản, nhân viên, cấp phát và báo cáo</li>
                            <li><strong>User:</strong> Xem thông tin tài sản được cấp, báo cáo sự cố</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Tạo người dùng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate username from employee selection
    const employeeSelect = document.getElementById('employee_id');
    const usernameInput = document.getElementById('username');
    
    employeeSelect.addEventListener('change', function() {
        if (this.value && !usernameInput.value) {
            const selectedOption = this.options[this.selectedIndex];
            const employeeName = selectedOption.text.split(' - ')[0];
            const username = employeeName.toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]/g, '');
            usernameInput.value = username;
        }
    });
});
</script>
@endsection