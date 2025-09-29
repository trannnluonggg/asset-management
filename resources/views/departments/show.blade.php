@extends('layouts.app')

@section('title', 'Chi tiết bộ phận')
@section('page-title', 'Chi tiết bộ phận: ' . $department->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building me-2"></i>
                    Thông tin bộ phận
                </h6>
                <div>
                    @if(auth()->user()->canManageAssets())
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning btn-sm">
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
                                <td><strong>Mã bộ phận:</strong></td>
                                <td>{{ $department->code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tên bộ phận:</strong></td>
                                <td>{{ $department->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Trưởng bộ phận:</strong></td>
                                <td>
                                    @if($department->manager)
                                        <a href="{{ route('employees.show', $department->manager) }}" class="text-decoration-none">
                                            {{ $department->manager->full_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    @if($department->is_active)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tổng số nhân viên:</strong></td>
                                <td>{{ $employeeCount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nhân viên đang làm việc:</strong></td>
                                <td>{{ $activeEmployeeCount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($department->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Mô tả:</strong></h6>
                        <p class="text-muted">{{ $department->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Employees List -->
        @if($department->employees->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>
                    Danh sách nhân viên
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Mã NV</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Chức vụ</th>
                                <th>Trạng thái</th>
                                <th>Tài sản đang sử dụng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->employees->sortBy('full_name') as $employee)
                            <tr>
                                <td>{{ $employee->employee_code }}</td>
                                <td>
                                    <a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">
                                        {{ $employee->full_name }}
                                    </a>
                                </td>
                                <td>{{ $employee->email ?? '-' }}</td>
                                <td>{{ $employee->position ?? '-' }}</td>
                                <td>
                                    @if($employee->status == 'active')
                                        <span class="badge bg-success">Đang làm việc</span>
                                    @else
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $employee->activeAssignments->count() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->canManageAssets())
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
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
                            <h4 class="text-primary">{{ $activeEmployeeCount }}</h4>
                            <small class="text-muted">Đang làm việc</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-secondary">{{ $employeeCount - $activeEmployeeCount }}</h4>
                        <small class="text-muted">Không hoạt động</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-info">{{ $department->employees->sum(function($employee) { return $employee->activeAssignments->count(); }) }}</h4>
                        <small class="text-muted">Tài sản đang sử dụng</small>
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
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    
                    <a href="{{ route('employees.create', ['department_id' => $department->id]) }}" class="btn btn-success">
                        <i class="fas fa-user-plus me-2"></i>Thêm nhân viên
                    </a>
                </div>
                @endif
                
                <hr>
                
                <div class="d-grid">
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection