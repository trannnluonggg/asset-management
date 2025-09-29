@extends('layouts.app')

@section('title', 'Dashboard - Livespo Asset Management')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng tài sản
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_assets'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tài sản khả dụng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['available_assets'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Đã giao
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['assigned_assets'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Bảo trì
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['maintenance_assets'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Assignments -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Giao tài sản gần đây
                </h6>
            </div>
            <div class="card-body">
                @if($recent_assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tài sản</th>
                                    <th>Nhân viên</th>
                                    <th>Ngày giao</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->asset->asset_code }}</strong><br>
                                        <small class="text-muted">{{ $assignment->asset->asset_name }}</small>
                                    </td>
                                    <td>{{ $assignment->employee->full_name }}</td>
                                    <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Chưa có giao tài sản nào.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Incidents -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Sự cố gần đây
                </h6>
            </div>
            <div class="card-body">
                @if($recent_incidents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tài sản</th>
                                    <th>Loại sự cố</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_incidents as $incident)
                                <tr>
                                    <td>
                                        <strong>{{ $incident->asset->asset_code }}</strong><br>
                                        <small class="text-muted">{{ $incident->asset->asset_name }}</small>
                                    </td>
                                    <td>{{ $incident->incident_type }}</td>
                                    <td>
                                        @if($incident->status == 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @elseif($incident->status == 'resolved')
                                            <span class="badge bg-success">Đã xử lý</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $incident->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Chưa có sự cố nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>
                    Thao tác nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('assets.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus me-2"></i>Thêm tài sản mới
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('qr.scan') }}" class="btn btn-info btn-block">
                            <i class="fas fa-qrcode me-2"></i>Quét QR Code
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('assets.index') }}" class="btn btn-success btn-block">
                            <i class="fas fa-search me-2"></i>Tìm kiếm tài sản
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-warning btn-block">
                            <i class="fas fa-chart-bar me-2"></i>Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-primary {
    color: #4e73df !important;
}
.text-success {
    color: #1cc88a !important;
}
.text-info {
    color: #36b9cc !important;
}
.text-warning {
    color: #f6c23e !important;
}
.btn-block {
    display: block;
    width: 100%;
}
</style>
@endsection