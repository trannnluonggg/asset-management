@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ')

@section('page-title', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Chỉnh sửa hồ sơ cá nhân
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
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
                        <div class="form-text">Tên đăng nhập phải là duy nhất trong hệ thống</div>
                    </div>

                    <!-- Employee Information (Read-only) -->
                    @if($user->employee)
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Thông tin nhân viên</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Mã nhân viên:</strong> {{ $user->employee->employee_code }}<br>
                                <strong>Họ tên:</strong> {{ $user->employee->full_name }}<br>
                                <strong>Email:</strong> {{ $user->employee->email }}
                            </div>
                            <div class="col-md-6">
                                <strong>Bộ phận:</strong> {{ $user->employee->department->name ?? 'Chưa phân bộ phận' }}<br>
                                <strong>Chức vụ:</strong> {{ $user->employee->position ?? 'Chưa xác định' }}<br>
                                <strong>Ngày vào làm:</strong> {{ $user->employee->hire_date ? $user->employee->hire_date->format('d/m/Y') : 'Chưa xác định' }}
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i>
                            Thông tin nhân viên chỉ có thể được cập nhật bởi quản trị viên.
                        </small>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tài khoản của bạn chưa được liên kết với thông tin nhân viên. 
                        Vui lòng liên hệ với quản trị viên để cập nhật thông tin.
                    </div>
                    @endif

                    <!-- Account Information -->
                    <div class="alert alert-light">
                        <h6><i class="fas fa-info-circle me-2"></i>Thông tin tài khoản:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Vai trò:</small><br>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role === 'hr')
                                    <span class="badge bg-warning">HR</span>
                                @else
                                    <span class="badge bg-info">User</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Tham gia từ:</small><br>
                                <strong>{{ $user->created_at->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Đăng nhập cuối:</small><br>
                                <strong>
                                    @if($user->last_login)
                                        {{ $user->last_login->format('d/m/Y H:i') }}
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <a href="{{ route('profile.change-password') }}" class="btn btn-warning me-2">
                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật hồ sơ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection