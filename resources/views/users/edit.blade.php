@extends('layouts.app')

@section('title', 'Chỉnh sửa người dùng')

@section('page-title', 'Chỉnh sửa người dùng')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Chỉnh sửa người dùng: {{ $user->username }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation">
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
                                <option value="{{ $employee->id }}" 
                                        {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' : '' }}>
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
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                Admin - Quản trị viên hệ thống
                            </option>
                            <option value="hr" {{ old('role', $user->role) === 'hr' ? 'selected' : '' }}>
                                HR - Nhân sự
                            </option>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>
                                User - Người dùng thông thường
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($user->isAdmin() && \App\Models\User::where('role', 'admin')->count() <= 1)
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Đây là admin cuối cùng trong hệ thống, không thể thay đổi vai trò.
                            </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt tài khoản
                            </label>
                        </div>
                        @if($user->id === auth()->id())
                            <div class="form-text text-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Không thể thay đổi trạng thái tài khoản đang đăng nhập.
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="alert alert-light">
                        <h6><i class="fas fa-info-circle me-2"></i>Thông tin tài khoản:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Tạo lúc:</small><br>
                                <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Đăng nhập cuối:</small><br>
                                <strong>
                                    @if($user->last_login)
                                        {{ $user->last_login->format('d/m/Y H:i') }}
                                    @else
                                        Chưa đăng nhập
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            @if($user->id !== auth()->id())
                            <a href="{{ route('users.reset-password', $user) }}" class="btn btn-warning me-2"
                               onclick="return confirm('Bạn có chắc chắn muốn reset mật khẩu cho người dùng này?')">
                                <i class="fas fa-key me-2"></i>Reset mật khẩu
                            </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                        </div>
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
    const roleSelect = document.getElementById('role');
    const isActiveCheckbox = document.getElementById('is_active');
    
    // Prevent changing role of last admin
    @if($user->isAdmin() && \App\Models\User::where('role', 'admin')->count() <= 1)
    roleSelect.disabled = true;
    @endif
    
    // Prevent deactivating current user
    @if($user->id === auth()->id())
    isActiveCheckbox.disabled = true;
    @endif
});
</script>
@endsection