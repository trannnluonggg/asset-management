@extends('layouts.app')

@section('title', 'Chi tiết báo cáo sự cố')
@section('page-title', 'Chi tiết báo cáo sự cố')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Thông tin sự cố
                </h6>
                <div>
                    @if($incident->status != 'closed' && auth()->user()->canManageAssets())
                    <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-warning btn-sm">
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
                                <td><strong>Tài sản:</strong></td>
                                <td>
                                    <a href="{{ route('assets.show', $incident->asset) }}" class="text-decoration-none">
                                        {{ $incident->asset->asset_code }} - {{ $incident->asset->asset_name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Danh mục:</strong></td>
                                <td><span class="badge bg-info">{{ $incident->asset->category->name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Loại sự cố:</strong></td>
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
                                            <span class="badge bg-secondary">Trục trặc</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ $incident->incident_type }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ngày xảy ra:</strong></td>
                                <td>{{ $incident->incident_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Người báo cáo:</strong></td>
                                <td>{{ $incident->reportedBy->full_name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    @switch($incident->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                            @break
                                        @case('investigating')
                                            <span class="badge bg-info">Đang điều tra</span>
                                            @break
                                        @case('resolved')
                                            <span class="badge bg-success">Đã giải quyết</span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-secondary">Đã đóng</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ $incident->status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ngày giải quyết:</strong></td>
                                <td>{{ $incident->resolved_date ? $incident->resolved_date->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Người giải quyết:</strong></td>
                                <td>{{ $incident->resolvedBy->full_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ngày tạo:</strong></td>
                                <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Mô tả sự cố:</strong></h6>
                        <p class="text-muted">{{ $incident->description }}</p>
                    </div>
                </div>
                
                @if($incident->resolution)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Giải pháp:</strong></h6>
                        <p class="text-muted">{{ $incident->resolution }}</p>
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
                    @if($incident->status != 'closed')
                    <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    @endif
                    
                    @if($incident->status == 'pending' || $incident->status == 'investigating')
                    <button class="btn btn-success" onclick="showResolveModal()">
                        <i class="fas fa-check me-2"></i>Giải quyết
                    </button>
                    @endif
                    
                    @if($incident->status == 'resolved')
                    <form action="{{ route('incidents.close', $incident) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-100" 
                                onclick="return confirm('Bạn có chắc chắn muốn đóng sự cố này?')">
                            <i class="fas fa-times me-2"></i>Đóng sự cố
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('assets.show', $incident->asset) }}" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>Xem tài sản
                    </a>
                </div>
                @endif
                
                <hr>
                
                <div class="d-grid">
                    <a href="{{ route('incidents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
@if($incident->status == 'pending' || $incident->status == 'investigating')
<div class="modal fade" id="resolveModal" tabindex="-1">
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
                        <label for="resolution" class="form-label">Giải pháp <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="resolution" name="resolution" rows="4" required
                                  placeholder="Mô tả cách giải quyết sự cố..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="asset_status" class="form-label">Trạng thái tài sản sau khi giải quyết <span class="text-danger">*</span></label>
                        <select class="form-select" id="asset_status" name="asset_status" required>
                            <option value="">Chọn trạng thái</option>
                            <option value="available">Khả dụng</option>
                            <option value="maintenance">Bảo trì</option>
                            <option value="retired">Ngừng sử dụng</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Giải quyết
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function showResolveModal() {
    const modal = new bootstrap.Modal(document.getElementById('resolveModal'));
    modal.show();
}
</script>
@endpush
@endsection