@extends('layouts.app')

@section('title', 'Quản lý báo cáo sự cố')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-exclamation-triangle me-2"></i>Quản lý báo cáo sự cố</h2>
    <a href="{{ route('incidents.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Báo cáo sự cố
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('incidents.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo mã tài sản, tên nhân viên..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="incident_type" class="form-select">
                        <option value="">Tất cả loại sự cố</option>
                        <option value="damage" {{ request('incident_type') == 'damage' ? 'selected' : '' }}>Hỏng hóc</option>
                        <option value="lost" {{ request('incident_type') == 'lost' ? 'selected' : '' }}>Mất</option>
                        <option value="theft" {{ request('incident_type') == 'theft' ? 'selected' : '' }}>Bị trộm</option>
                        <option value="malfunction" {{ request('incident_type') == 'malfunction' ? 'selected' : '' }}>Trục trặc</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Đang điều tra</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Incidents Table -->
<div class="card">
    <div class="card-body">
        @if($incidents->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã tài sản</th>
                            <th>Tên tài sản</th>
                            <th>Loại sự cố</th>
                            <th>Người báo cáo</th>
                            <th>Ngày xảy ra</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidents as $incident)
                            <tr>
                                <td><strong>{{ $incident->asset->asset_code }}</strong></td>
                                <td>{{ $incident->asset->asset_name }}</td>
                                <td>
                                    @switch($incident->incident_type)
                                        @case('damage')
                                            <span class="badge bg-warning">Hỏng hóc</span>
                                            @break
                                        @case('lost')
                                            <span class="badge bg-danger">Mất</span>
                                            @break
                                        @case('theft')
                                            <span class="badge bg-dark">Bị trộm</span>
                                            @break
                                        @case('malfunction')
                                            <span class="badge bg-info">Trục trặc</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $incident->reportedBy->full_name ?? 'N/A' }}</strong>
                                        @if($incident->reportedBy)
                                            <br>
                                            <small class="text-muted">{{ $incident->reportedBy->department->name ?? 'N/A' }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $incident->incident_date->format('d/m/Y') }}</td>
                                <td>
                                    @switch($incident->status)
                                        @case('pending')
                                            <span class="badge bg-secondary">Chờ xử lý</span>
                                            @break
                                        @case('investigating')
                                            <span class="badge bg-primary">Đang điều tra</span>
                                            @break
                                        @case('resolved')
                                            <span class="badge bg-success">Đã giải quyết</span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-dark">Đã đóng</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($incident->status !== 'closed')
                                            <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($incident->status === 'pending' || $incident->status === 'investigating')
                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#resolveModal{{ $incident->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        @if($incident->status === 'resolved')
                                            <form action="{{ route('incidents.close', $incident) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-dark" onclick="return confirm('Bạn có chắc chắn muốn đóng sự cố này?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Resolve Modal -->
                            @if($incident->status === 'pending' || $incident->status === 'investigating')
                            <div class="modal fade" id="resolveModal{{ $incident->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('incidents.resolve', $incident) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Giải quyết sự cố</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="resolution{{ $incident->id }}" class="form-label">Cách giải quyết <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="resolution{{ $incident->id }}" name="resolution" rows="3" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="asset_status{{ $incident->id }}" class="form-label">Trạng thái tài sản sau khi giải quyết <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="asset_status{{ $incident->id }}" name="asset_status" required>
                                                        <option value="">Chọn trạng thái</option>
                                                        <option value="available">Khả dụng</option>
                                                        <option value="maintenance">Bảo trì</option>
                                                        <option value="retired">Ngừng sử dụng</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-success">Giải quyết</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $incidents->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy báo cáo sự cố nào</h5>
                <p class="text-muted">Hãy thử thay đổi điều kiện tìm kiếm hoặc tạo báo cáo mới.</p>
            </div>
        @endif
    </div>
</div>
@endsection