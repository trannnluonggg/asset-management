@extends('layouts.app')

@section('title', 'Báo cáo & Thống kê')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-bar"></i> Báo cáo & Thống kê</h2>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $totalAssets }}</h4>
                            <p class="mb-0">Tổng tài sản</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $availableAssets }}</h4>
                            <p class="mb-0">Khả dụng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $assignedAssets }}</h4>
                            <p class="mb-0">Đã giao</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $maintenanceAssets + $retiredAssets }}</h4>
                            <p class="mb-0">Bảo trì/Ngừng dùng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ tài sản theo danh mục -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-pie"></i> Tài sản theo danh mục</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ tài sản theo trạng thái -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-doughnut"></i> Tài sản theo trạng thái</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ thống kê theo tháng -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line"></i> Thống kê 12 tháng gần đây</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tài sản giá trị cao -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-dollar-sign"></i> Tài sản có giá trị cao nhất</h5>
                </div>
                <div class="card-body">
                    @if($highValueAssets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mã tài sản</th>
                                        <th>Tên tài sản</th>
                                        <th>Giá trị</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($highValueAssets as $asset)
                                    <tr>
                                        <td>
                                            <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                                {{ $asset->asset_code }}
                                            </a>
                                        </td>
                                        <td>{{ $asset->asset_name }}</td>
                                        <td>{{ number_format($asset->purchase_price, 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Chưa có dữ liệu giá trị tài sản.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tài sản sắp hết bảo hành -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-exclamation-triangle text-warning"></i> Sắp hết bảo hành (3 tháng tới)</h5>
                </div>
                <div class="card-body">
                    @if($expiringWarranty->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mã tài sản</th>
                                        <th>Tên tài sản</th>
                                        <th>Hết hạn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringWarranty as $asset)
                                    <tr>
                                        <td>
                                            <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                                {{ $asset->asset_code }}
                                            </a>
                                        </td>
                                        <td>{{ $asset->asset_name }}</td>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ $asset->warranty_expiry->format('d/m/Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Không có tài sản nào sắp hết bảo hành.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hoạt động gần đây -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-history"></i> Hoạt động gần đây</h5>
                    <a href="{{ route('reports.activities') }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Tài sản</th>
                                        <th>Hoạt động</th>
                                        <th>Người thực hiện</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities->take(10) as $activity)
                                    <tr>
                                        <td>{{ $activity->action_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('assets.show', $activity->asset) }}" class="text-decoration-none">
                                                {{ $activity->asset->asset_code }}
                                            </a>
                                        </td>
                                        <td>
                                            @switch($activity->action_type)
                                                @case('create')
                                                    <span class="badge bg-success">Tạo mới</span>
                                                    @break
                                                @case('update')
                                                    <span class="badge bg-info">Cập nhật</span>
                                                    @break
                                                @case('assign')
                                                    <span class="badge bg-warning">Giao tài sản</span>
                                                    @break
                                                @case('return')
                                                    <span class="badge bg-secondary">Thu hồi</span>
                                                    @break
                                                @case('maintenance')
                                                    <span class="badge bg-danger">Bảo trì</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ $activity->action_type }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $activity->performedBy->full_name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($activity->notes, 50) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Chưa có hoạt động nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Liên kết báo cáo chi tiết -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-alt"></i> Báo cáo chi tiết</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('reports.assets') }}" class="btn btn-outline-primary btn-lg w-100 mb-3">
                                <i class="fas fa-boxes"></i><br>
                                Báo cáo tài sản
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('reports.activities') }}" class="btn btn-outline-success btn-lg w-100 mb-3">
                                <i class="fas fa-history"></i><br>
                                Báo cáo hoạt động
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('reports.export') }}" class="btn btn-outline-info btn-lg w-100 mb-3">
                                <i class="fas fa-download"></i><br>
                                Xuất dữ liệu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ tài sản theo danh mục
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($assetsByCategory->pluck('category_name')) !!},
        datasets: [{
            data: {!! json_encode($assetsByCategory->pluck('total')) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB', 
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Biểu đồ tài sản theo trạng thái
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($assetsByStatus->pluck('status')->map(function($status) {
            $statusMap = [
                'available' => 'Khả dụng',
                'assigned' => 'Đã giao', 
                'maintenance' => 'Bảo trì',
                'retired' => 'Ngừng sử dụng'
            ];
            return $statusMap[$status] ?? $status;
        })) !!},
        datasets: [{
            data: {!! json_encode($assetsByStatus->pluck('total')) !!},
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545', 
                '#6c757d'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Biểu đồ thống kê theo tháng
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($monthlyStats)->pluck('month_name')) !!},
        datasets: [{
            label: 'Tài sản mới',
            data: {!! json_encode(collect($monthlyStats)->pluck('created')) !!},
            borderColor: '#36A2EB',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4
        }, {
            label: 'Hoạt động',
            data: {!! json_encode(collect($monthlyStats)->pluck('activities')) !!},
            borderColor: '#FF6384',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection