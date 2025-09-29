@extends('layouts.app')

@section('title', 'Chi tiết cấp phát tài sản')
@section('page-title', 'Chi tiết cấp phát tài sản')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    Thông tin cấp phát
                </h6>
                <div>
                    @if($assignment->status == 'active' && auth()->user()->canManageAssets())
                    <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <a href="{{ route('assignments.return.form', $assignment) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-undo me-2"></i>Thu hồi
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Tài sản:</strong></td>
                                <td>
                                    <a href="{{ route('assets.show', $assignment->asset) }}" class="text-decoration-none">
                                        {{ $assignment->asset->asset_code }} - {{ $assignment->asset->asset_name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Danh mục:</strong></td>
                                <td><span class="badge bg-info">{{ $assignment->asset->category->name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Nhân viên:</strong></td>
                                <td>
                                    <a href="{{ route('employees.show', $assignment->employee) }}" class="text-decoration-none">
                                        {{ $assignment->employee->full_name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Mã nhân viên:</strong></td>
                                <td>{{ $assignment->employee->employee_code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bộ phận:</strong></td>
                                <td>{{ $assignment->employee->department->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Ngày cấp phát:</strong></td>
                                <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ngày trả dự kiến:</strong></td>
                                <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ngày trả thực tế:</strong></td>
                                <td>{{ $assignment->actual_return_date ? $assignment->actual_return_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Người cấp phát:</strong></td>
                                <td>{{ $assignment->assignedBy->full_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    @if($assignment->status == 'active')
                                        <span class="badge bg-success">Đang sử dụng</span>
                                    @else
                                        <span class="badge bg-secondary">Đã trả</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($assignment->assignment_notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Ghi chú cấp phát:</strong></h6>
                        <p class="text-muted">{{ $assignment->assignment_notes }}</p>
                    </div>
                </div>
                @endif
                
                @if($assignment->return_notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Ghi chú trả lại:</strong></h6>
                        <p class="text-muted">{{ $assignment->return_notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
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
                    @if($assignment->status == 'active')
                    <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <a href="{{ route('assignments.return.form', $assignment) }}" class="btn btn-danger">
                        <i class="fas fa-undo me-2"></i>Thu hồi tài sản
                    </a>
                    @endif
                    
                    <a href="{{ route('assets.show', $assignment->asset) }}" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>Xem tài sản
                    </a>
                    
                    <a href="{{ route('employees.show', $assignment->employee) }}" class="btn btn-success">
                        <i class="fas fa-user me-2"></i>Xem nhân viên
                    </a>
                </div>
                @endif
                
                <hr>
                
                <div class="d-grid">
                    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection