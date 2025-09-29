@extends('layouts.app')

@section('title', 'Báo cáo hoạt động')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history"></i> Báo cáo hoạt động</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter"></i> Bộ lọc</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.activities') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Loại hoạt động:</label>
                            <select name="action_type" class="form-select">
                                <option value="">Tất cả hoạt động</option>
                                <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>Tạo mới</option>
                                <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Cập nhật</option>
                                <option value="assign" {{ request('action_type') == 'assign' ? 'selected' : '' }}>Giao tài sản</option>
                                <option value="return" {{ request('action_type') == 'return' ? 'selected' : '' }}>Thu hồi</option>
                                <option value="maintenance" {{ request('action_type') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Từ ngày:</label>
                            <input type="date" name="date_from" class="form-control" 
                                value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Đến ngày:</label>
                            <input type="date" name="date_to" class="form-control" 
                                value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tài sản:</label>
                            <select name="asset_id" class="form-select">
                                <option value="">Tất cả tài sản</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                        {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_code }} - {{ $asset->asset_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Người thực hiện:</label>
                            <select name="user_id" class="form-select">
                                <option value="">Tất cả người dùng</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('reports.activities') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Kết quả -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-list"></i> Lịch sử hoạt động ({{ $activities->total() }} kết quả)</h5>
        </div>
        <div class="card-body">
            @if($activities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Tài sản</th>
                                <th>Loại hoạt động</th>
                                <th>Người thực hiện</th>
                                <th>Mô tả</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            <tr>
                                <td>
                                    <div>{{ $activity->action_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $activity->action_date->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('assets.show', $activity->asset) }}" class="text-decoration-none">
                                        <strong>{{ $activity->asset->asset_code }}</strong>
                                    </a>
                                    <div class="small text-muted">{{ $activity->asset->asset_name }}</div>
                                </td>
                                <td>
                                    @switch($activity->action_type)
                                        @case('create')
                                            <span class="badge bg-success">
                                                <i class="fas fa-plus"></i> Tạo mới
                                            </span>
                                            @break
                                        @case('update')
                                            <span class="badge bg-info">
                                                <i class="fas fa-edit"></i> Cập nhật
                                            </span>
                                            @break
                                        @case('assign')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-user-plus"></i> Giao tài sản
                                            </span>
                                            @break
                                        @case('return')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-undo"></i> Thu hồi
                                            </span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-tools"></i> Bảo trì
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">
                                                {{ $activity->action_type }}
                                            </span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($activity->performedBy)
                                        <div>{{ $activity->performedBy->full_name }}</div>
                                        <small class="text-muted">{{ $activity->performedBy->employee_id }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $activity->description ?? 'N/A' }}</td>
                                <td>
                                    @if($activity->notes)
                                        <span data-bs-toggle="tooltip" title="{{ $activity->notes }}">
                                            {{ Str::limit($activity->notes, 50) }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-center">
                    {{ $activities->appends(request()->query())->links() }}
                </div>

                <!-- Thống kê tóm tắt -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Thống kê tóm tắt:</h6>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Tổng hoạt động:</strong> {{ $activities->total() }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Tạo mới:</strong> 
                                    {{ $activities->where('action_type', 'create')->count() }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Cập nhật:</strong> 
                                    {{ $activities->where('action_type', 'update')->count() }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Giao tài sản:</strong> 
                                    {{ $activities->where('action_type', 'assign')->count() }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Thu hồi:</strong> 
                                    {{ $activities->where('action_type', 'return')->count() }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Bảo trì:</strong> 
                                    {{ $activities->where('action_type', 'maintenance')->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Không tìm thấy hoạt động nào phù hợp với bộ lọc.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Khởi tạo tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection