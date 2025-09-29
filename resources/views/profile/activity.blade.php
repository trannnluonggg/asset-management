@extends('layouts.app')

@section('title', 'Hoạt động của tôi')

@section('page-title', 'Hoạt động của tôi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Lịch sử hoạt động
                </h5>
                <a href="{{ route('profile.show') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Quay lại hồ sơ
                </a>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0 || $incidents->count() > 0)
                    <div class="timeline">
                        @php
                            $activities = collect();
                            
                            // Add assignments to activities
                            foreach($assignments as $assignment) {
                                $activities->push([
                                    'type' => 'assignment',
                                    'date' => $assignment->created_at,
                                    'title' => 'Cấp phát tài sản',
                                    'description' => 'Được cấp phát tài sản: ' . $assignment->asset->name,
                                    'asset_code' => $assignment->asset->asset_code ?? 'N/A',
                                    'status' => $assignment->status,
                                    'icon' => 'fas fa-handshake',
                                    'color' => 'primary'
                                ]);
                            }
                            
                            // Add incidents to activities
                            foreach($incidents as $incident) {
                                $activities->push([
                                    'type' => 'incident',
                                    'date' => $incident->created_at,
                                    'title' => 'Báo cáo sự cố',
                                    'description' => $incident->title,
                                    'asset_code' => $incident->asset->asset_code ?? 'N/A',
                                    'status' => $incident->status,
                                    'icon' => 'fas fa-exclamation-triangle',
                                    'color' => 'warning'
                                ]);
                            }
                            
                            // Sort by date descending
                            $activities = $activities->sortByDesc('date');
                        @endphp
                        
                        @foreach($activities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <div class="bg-{{ $activity['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-content">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">{{ $activity['title'] }}</h6>
                                                <small class="text-muted">
                                                    {{ $activity['date']->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-2">{{ $activity['description'] }}</p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Mã tài sản:</strong> {{ $activity['asset_code'] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Trạng thái:</strong> 
                                                        @if($activity['type'] === 'assignment')
                                                            @if($activity['status'] === 'active')
                                                                <span class="badge bg-success">Đang sử dụng</span>
                                                            @elseif($activity['status'] === 'returned')
                                                                <span class="badge bg-secondary">Đã trả lại</span>
                                                            @else
                                                                <span class="badge bg-warning">{{ $activity['status'] }}</span>
                                                            @endif
                                                        @else
                                                            @if($activity['status'] === 'pending')
                                                                <span class="badge bg-warning">Đang chờ xử lý</span>
                                                            @elseif($activity['status'] === 'in_progress')
                                                                <span class="badge bg-info">Đang xử lý</span>
                                                            @elseif($activity['status'] === 'resolved')
                                                                <span class="badge bg-success">Đã giải quyết</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ $activity['status'] }}</span>
                                                            @endif
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $activity['date']->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-history fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Chưa có hoạt động nào</h5>
                        <p class="text-muted">Bạn chưa có hoạt động nào trong hệ thống.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>
                            Về trang chủ
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $assignments->where('status', 'active')->count() }}</h4>
                <small>Tài sản đang sử dụng</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $assignments->where('status', 'returned')->count() }}</h4>
                <small>Tài sản đã trả lại</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $incidents->where('status', 'pending')->count() }}</h4>
                <small>Sự cố chờ xử lý</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $incidents->where('status', 'resolved')->count() }}</h4>
                <small>Sự cố đã giải quyết</small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    z-index: 1;
}

.timeline-content {
    margin-left: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item:last-child::after {
    content: '';
    position: absolute;
    left: -30px;
    bottom: -15px;
    width: 40px;
    height: 2px;
    background: #dee2e6;
}
</style>
@endsection