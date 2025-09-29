@extends('layouts.app')

@section('title', 'Báo cáo tài sản')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes"></i> Báo cáo tài sản</h2>
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
            <form method="GET" action="{{ route('reports.assets') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Danh mục:</label>
                            <select name="category_id" class="form-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái:</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Khả dụng</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Đã giao</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                                <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Ngừng sử dụng</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Từ ngày mua:</label>
                            <input type="date" name="purchase_from" class="form-control" 
                                value="{{ request('purchase_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Đến ngày mua:</label>
                            <input type="date" name="purchase_to" class="form-control" 
                                value="{{ request('purchase_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Giá từ:</label>
                            <input type="number" name="price_from" class="form-control" 
                                value="{{ request('price_from') }}" placeholder="VNĐ">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Giá đến:</label>
                            <input type="number" name="price_to" class="form-control" 
                                value="{{ request('price_to') }}" placeholder="VNĐ">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('reports.assets') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Xóa bộ lọc
                                </a>
                                <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Xuất Excel
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
            <h5><i class="fas fa-list"></i> Danh sách tài sản ({{ $assets->count() }} kết quả)</h5>
        </div>
        <div class="card-body">
            @if($assets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã tài sản</th>
                                <th>Tên tài sản</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Model</th>
                                <th>Serial</th>
                                <th>Trạng thái</th>
                                <th>Vị trí</th>
                                <th>Người được giao</th>
                                <th>Ngày mua</th>
                                <th>Giá mua</th>
                                <th>Bảo hành</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets as $asset)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                        <strong>{{ $asset->asset_code }}</strong>
                                    </a>
                                </td>
                                <td>{{ $asset->asset_name }}</td>
                                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                <td>{{ $asset->brand ?? 'N/A' }}</td>
                                <td>{{ $asset->model ?? 'N/A' }}</td>
                                <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                                <td>
                                    @switch($asset->status)
                                        @case('available')
                                            <span class="badge bg-success">Khả dụng</span>
                                            @break
                                        @case('assigned')
                                            <span class="badge bg-warning">Đã giao</span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge bg-danger">Bảo trì</span>
                                            @break
                                        @case('retired')
                                            <span class="badge bg-secondary">Ngừng sử dụng</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ $asset->status }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $asset->location ?? 'N/A' }}</td>
                                <td>{{ $asset->assignedUser->full_name ?? 'N/A' }}</td>
                                <td>{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($asset->purchase_price)
                                        {{ number_format($asset->purchase_price, 0, ',', '.') }} VNĐ
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($asset->warranty_expiry)
                                        @if($asset->warranty_expiry < now())
                                            <span class="badge bg-danger">Hết hạn</span>
                                        @elseif($asset->warranty_expiry <= now()->addMonths(3))
                                            <span class="badge bg-warning">{{ $asset->warranty_expiry->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $asset->warranty_expiry->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Thống kê tóm tắt -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Thống kê tóm tắt:</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Tổng số tài sản:</strong> {{ $assets->count() }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Khả dụng:</strong> {{ $assets->where('status', 'available')->count() }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Đã giao:</strong> {{ $assets->where('status', 'assigned')->count() }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Tổng giá trị:</strong> 
                                    {{ number_format($assets->sum('purchase_price'), 0, ',', '.') }} VNĐ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Không tìm thấy tài sản nào phù hợp với bộ lọc.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection