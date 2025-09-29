@extends('layouts.app')

@section('title', 'Quản lý tài sản - Livespo Asset Management')
@section('page-title', 'Quản lý tài sản')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-laptop me-2"></i>
            Danh sách tài sản
        </h6>
        @if(auth()->user()->canManageAssets())
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm tài sản
        </a>
        @endif
    </div>
    
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('assets.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Tìm kiếm:</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Mã tài sản, tên, số serial...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category">Danh mục:</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Trạng thái:</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Khả dụng</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Đã giao</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Ngừng sử dụng</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tìm
                            </button>
                            <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Assets Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mã tài sản</th>
                        <th>Tên tài sản</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Trạng thái</th>
                        <th>Vị trí</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>
                            <strong>{{ $asset->asset_code }}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-qrcode"></i> {{ $asset->qr_code }}
                            </small>
                        </td>
                        <td>
                            {{ $asset->asset_name }}
                            @if($asset->model)
                                <br><small class="text-muted">{{ $asset->model }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $asset->category->name }}</span>
                        </td>
                        <td>{{ $asset->brand ?? '-' }}</td>
                        <td>
                            @switch($asset->status)
                                @case('available')
                                    <span class="badge bg-success">Khả dụng</span>
                                    @break
                                @case('assigned')
                                    <span class="badge bg-primary">Đã giao</span>
                                    @break
                                @case('maintenance')
                                    <span class="badge bg-warning">Bảo trì</span>
                                    @break
                                @case('retired')
                                    <span class="badge bg-danger">Ngừng sử dụng</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $asset->status }}</span>
                            @endswitch
                        </td>
                        <td>{{ $asset->location ?? '-' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('assets.show', $asset) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->canManageAssets())
                                <a href="{{ route('assets.edit', $asset) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('assets.destroy', $asset) }}" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài sản này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <br>
                            Không tìm thấy tài sản nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assets->hasPages())
        <div class="d-flex justify-content-center">
            {{ $assets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection