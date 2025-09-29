@extends('layouts.app')

@section('title', 'Thông báo')

@section('page-title', 'Thông báo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-bell me-2"></i>
                    Thông báo của tôi
                </h5>
                <div>
                    @if($notifications->where('is_read', false)->count() > 0)
                        <form method="POST" action="{{ route('profile.notifications.read-all') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check-double me-1"></i>
                                Đánh dấu tất cả đã đọc
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('profile.show') }}" class="btn btn-sm btn-secondary ms-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại hồ sơ
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item {{ !$notification->is_read ? 'bg-light border-start border-primary border-3' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                @if($notification->type === 'asset_assignment')
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-handshake"></i>
                                                    </div>
                                                @elseif($notification->type === 'asset_return')
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-undo"></i>
                                                    </div>
                                                @elseif($notification->type === 'incident_report')
                                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </div>
                                                @elseif($notification->type === 'system')
                                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-info-circle"></i>
                                                    </div>
                                                @else
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-bell"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                                    {{ $notification->title }}
                                                    @if(!$notification->is_read)
                                                        <span class="badge bg-primary ms-2">Mới</span>
                                                    @endif
                                                </h6>
                                                <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        
                                        @if($notification->data && is_array($notification->data))
                                            <div class="mt-2">
                                                @if(isset($notification->data['asset_name']))
                                                    <small class="text-muted">
                                                        <strong>Tài sản:</strong> {{ $notification->data['asset_name'] }}
                                                    </small>
                                                @endif
                                                @if(isset($notification->data['asset_code']))
                                                    <small class="text-muted ms-3">
                                                        <strong>Mã:</strong> {{ $notification->data['asset_code'] }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="ms-3">
                                        @if(!$notification->is_read)
                                            <form method="POST" action="{{ route('profile.notifications.read', $notification->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Đánh dấu đã đọc">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($notification->data && isset($notification->data['url']))
                                            <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-secondary ms-1" title="Xem chi tiết">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-bell-slash fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Không có thông báo nào</h5>
                        <p class="text-muted">Bạn chưa có thông báo nào trong hệ thống.</p>
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

<!-- Statistics Card -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $notifications->where('is_read', false)->count() }}</h4>
                <small>Chưa đọc</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $notifications->where('is_read', true)->count() }}</h4>
                <small>Đã đọc</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $notifications->count() }}</h4>
                <small>Tổng cộng</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $notifications->where('created_at', '>=', now()->subDays(7))->count() }}</h4>
                <small>Tuần này</small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh notifications every 30 seconds
    setInterval(function() {
        // Only refresh if there are unread notifications
        if (document.querySelector('.bg-light.border-start')) {
            location.reload();
        }
    }, 30000);
});
</script>
@endsection