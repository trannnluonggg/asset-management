@extends('layouts.app')

@section('title', 'Chi tiết người dùng')

@section('page-title', 'Chi tiết người dùng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Thông tin người dùng: {{ $user->username }}
                </h5>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Chỉnh sửa
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user-circle me-2"></i>Thông tin tài khoản</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Tên đăng nhập:</strong></td>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <td><strong>Vai trò:</strong></td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role === 'hr')
                                        <span class="badge bg-warning">HR</span>
                                    @else
                                        <span class="badge bg-info">User</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tạo lúc:</strong></td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Đăng nhập cuối:</strong></td>
                                <td>
                                    @if($user->last_login)
                                        {{ $user->last_login->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Chưa đăng nhập</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-id-card me-2"></i>Thông tin nhân viên</h6>
                        @if($user->employee)
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Mã nhân viên:</strong></td>
                                    <td>{{ $user->employee->employee_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td>{{ $user->employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->employee->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bộ phận:</strong></td>
                                    <td>
                                        @if($user->employee->department)
                                            {{ $user->employee->department->name }}
                                        @else
                                            <span class="text-muted">Chưa phân bộ phận</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Chức vụ:</strong></td>
                                    <td>{{ $user->employee->position ?? 'Chưa xác định' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày vào làm:</strong></td>
                                    <td>{{ $user->employee->hire_date ? $user->employee->hire_date->format('d/m/Y') : 'Chưa xác định' }}</td>
                                </tr>
                            </table>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Tài khoản này chưa được liên kết với thông tin nhân viên.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Role Permissions -->
                <div class="mt-4">
                    <h6><i class="fas fa-shield-alt me-2"></i>Quyền hạn</h6>
                    <div class="alert alert-light">
                        @if($user->role === 'admin')
                            <h6 class="text-danger">Quản trị viên hệ thống</h6>
                            <ul class="mb-0">
                                <li>Toàn quyền quản lý hệ thống</li>
                                <li>Quản lý người dùng và phân quyền</li>
                                <li>Cấu hình hệ thống và cài đặt</li>
                                <li>Quản lý tài sản, nhân viên, cấp phát</li>
                                <li>Xem tất cả báo cáo và thống kê</li>
                            </ul>
                        @elseif($user->role === 'hr')
                            <h6 class="text-warning">Nhân sự</h6>
                            <ul class="mb-0">
                                <li>Quản lý tài sản và danh mục</li>
                                <li>Quản lý nhân viên và bộ phận</li>
                                <li>Cấp phát và thu hồi tài sản</li>
                                <li>Xử lý báo cáo sự cố</li>
                                <li>Xem báo cáo và thống kê</li>
                            </ul>
                        @else
                            <h6 class="text-info">Người dùng thông thường</h6>
                            <ul class="mb-0">
                                <li>Xem thông tin tài sản được cấp</li>
                                <li>Quét QR code để xem chi tiết tài sản</li>
                                <li>Báo cáo sự cố tài sản</li>
                                <li>Xem lịch sử sử dụng tài sản</li>
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                    <div>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Bạn có chắc chắn muốn reset mật khẩu cho người dùng này?')">
                                <i class="fas fa-key me-2"></i>Reset mật khẩu
                            </button>
                        </form>
                        <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline me-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}"
                                    onclick="return confirm('Bạn có chắc chắn muốn {{ $user->is_active ? 'vô hiệu hóa' : 'kích hoạt' }} tài khoản này?')">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-2"></i>
                                {{ $user->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Recent Notifications -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-bell me-2"></i>
                    Thông báo gần đây
                </h6>
            </div>
            <div class="card-body">
                @if($user->notifications->count() > 0)
                    @foreach($user->notifications->take(5) as $notification)
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            @if($notification->type === 'assignment')
                                <i class="fas fa-handshake text-primary"></i>
                            @elseif($notification->type === 'incident')
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            @elseif($notification->type === 'system')
                                <i class="fas fa-cog text-info"></i>
                            @else
                                <i class="fas fa-bell text-secondary"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <div class="small">
                                {{ $notification->message }}
                            </div>
                            <div class="text-muted small">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                        @if(!$notification->is_read)
                            <span class="badge bg-primary rounded-pill">Mới</span>
                        @endif
                    </div>
                    @endforeach
                    
                    @if($user->notifications->count() > 5)
                    <div class="text-center">
                        <small class="text-muted">
                            Và {{ $user->notifications->count() - 5 }} thông báo khác...
                        </small>
                    </div>
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">Chưa có thông báo nào</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection