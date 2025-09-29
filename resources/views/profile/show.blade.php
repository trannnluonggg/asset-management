@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('page-title', 'Hồ sơ cá nhân')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Thông tin cá nhân
                </h5>
                <div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
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
                                    <span class="badge bg-success">Hoạt động</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tham gia từ:</strong></td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Đăng nhập cuối:</strong></td>
                                <td>
                                    @if($user->last_login)
                                        {{ $user->last_login->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Chưa có thông tin</span>
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
                                Tài khoản của bạn chưa được liên kết với thông tin nhân viên. 
                                Vui lòng liên hệ với quản trị viên để cập nhật thông tin.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-4">
                    <h6><i class="fas fa-bolt me-2"></i>Thao tác nhanh</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('profile.change-password') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-key me-2"></i>
                                Đổi mật khẩu
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('profile.notifications') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-bell me-2"></i>
                                Thông báo
                                @if($user->unreadNotifications->count() > 0)
                                    <span class="badge bg-danger">{{ $user->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('profile.activity') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-history me-2"></i>
                                Hoạt động
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('qr.scan') }}" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-qrcode me-2"></i>
                                Quét QR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Account Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Thống kê tài khoản
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $user->notifications->count() }}</h4>
                            <small class="text-muted">Tổng thông báo</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $user->unreadNotifications->count() }}</h4>
                        <small class="text-muted">Chưa đọc</small>
                    </div>
                </div>
                <hr>
                @if($user->employee)
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-success">{{ $user->employee->assetAssignments->where('status', 'active')->count() }}</h4>
                            <small class="text-muted">Tài sản đang sử dụng</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ $user->employee->reportedIncidents->count() }}</h4>
                        <small class="text-muted">Báo cáo sự cố</small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Hoạt động gần đây
                </h6>
            </div>
            <div class="card-body">
                @if($user->employee && ($user->employee->assetAssignments->count() > 0 || $user->employee->reportedIncidents->count() > 0))
                    @php
                        $recentActivities = collect();
                        
                        // Add recent assignments
                        foreach($user->employee->assetAssignments->take(3) as $assignment) {
                            $recentActivities->push([
                                'type' => 'assignment',
                                'icon' => 'fas fa-handshake text-primary',
                                'message' => 'Được cấp phát tài sản: ' . $assignment->asset->name,
                                'date' => $assignment->created_at
                            ]);
                        }
                        
                        // Add recent incidents
                        foreach($user->employee->reportedIncidents->take(3) as $incident) {
                            $recentActivities->push([
                                'type' => 'incident',
                                'icon' => 'fas fa-exclamation-triangle text-warning',
                                'message' => 'Báo cáo sự cố: ' . $incident->title,
                                'date' => $incident->created_at
                            ]);
                        }
                        
                        $recentActivities = $recentActivities->sortByDesc('date')->take(5);
                    @endphp
                    
                    @foreach($recentActivities as $activity)
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <i class="{{ $activity['icon'] }}"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <div class="small">{{ $activity['message'] }}</div>
                            <div class="text-muted small">{{ $activity['date']->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="text-center">
                        <a href="{{ route('profile.activity') }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả hoạt động
                        </a>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <p class="mb-0">Chưa có hoạt động nào</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection