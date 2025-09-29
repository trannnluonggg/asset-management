@extends('layouts.app')

@section('title', 'Chi tiết nhân viên')
@section('page-title', 'Chi tiết nhân viên: ' . $employee->full_name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user me-2"></i>
                    Thông tin nhân viên
                </h6>
                <div>
                    @if(auth()->user()->canManageAssets())
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Mã nhân viên:</strong></td>
                                <td>{{ $employee->employee_code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Họ và tên:</strong></td>
                                <td>{{ $employee->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $employee->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Số điện thoại:</strong></td>
                                <td>{{ $employee->phone ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Bộ phận:</strong></td>
                                <td>
                                    <a href="{{ route('departments.show', $employee->department) }}" class="text-decoration-none">
                                        {{ $employee->department->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Chức vụ:</strong></td>
                                <td>{{ $employee->position ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ngày vào làm:</strong></td>
                                <td>{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    @if($employee->status == 'active')
                                        <span class="badge bg-success">Đang làm việc</span>
                                    @else
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Assignments -->
        @if($employee->activeAssignments->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-laptop me-2"></i>
                    Tài sản đang sử dụng
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Mã tài sản</th>
                                <th>Tên tài sản</th>
                                <th>Danh mục</th>
                                <th>Ngày giao</th>
                                <th>Ngày trả dự kiến</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->activeAssignments as $assignment)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $assignment->asset) }}" class="text-decoration-none">
                                        {{ $assignment->asset->asset_code }}
                                    </a>
                                </td>
                                <td>{{ $assignment->asset->asset_name }}</td>
                                <td><span class="badge bg-info">{{ $assignment->asset->category->name }}</span></td>
                                <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                                <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->canManageAssets())
                                    <a href="{{ route('assignments.return.form', $assignment) }}" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Assignment History -->
        @if($employee->assignments->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>
                    Lịch sử cấp phát tài sản
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tài sản</th>
                                <th>Ngày giao</th>
                                <th>Ngày trả dự kiến</th>
                                <th>Ngày trả thực tế</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->assignments->sortByDesc('assigned_date') as $assignment)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $assignment->asset) }}" class="text-decoration-none">
                                        {{ $assignment->asset->asset_code }} - {{ $assignment->asset->asset_name }}
                                    </a>
                                </td>
                                <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                                <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $assignment->actual_return_date ? $assignment->actual_return_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($assignment->status == 'active')
                                        <span class="badge bg-success">Đang sử dụng</span>
                                    @else
                                        <span class="badge bg-secondary">Đã trả</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>
                    Thống kê
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $employee->activeAssignments->count() }}</h4>
                            <small class="text-muted">Đang sử dụng</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $employee->assignments->where('status', 'returned')->count() }}</h4>
                        <small class="text-muted">Đã trả</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>
                    Thao tác nhanh
                </h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->canManageAssets())
                <div class="d-grid gap-2">
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    
                    <a href="{{ route('assignments.create', ['employee_id' => $employee->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Cấp phát tài sản
                    </a>
                    
                    <a href="{{ route('departments.show', $employee->department) }}" class="btn btn-info">
                        <i class="fas fa-building me-2"></i>Xem bộ phận
                    </a>
                </div>
                @endif
                
                <hr>
                
                <div class="d-grid">
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection