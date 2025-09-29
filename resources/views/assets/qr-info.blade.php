<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài sản - {{ $asset->asset_code }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .asset-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .asset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .asset-code {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .asset-name {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .info-section {
            padding: 30px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            display: flex;
            align-items: center;
        }
        .info-label i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .info-value {
            font-weight: 500;
            color: #333;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .status-available { background: #d4edda; color: #155724; }
        .status-assigned { background: #fff3cd; color: #856404; }
        .status-maintenance { background: #f8d7da; color: #721c24; }
        .status-retired { background: #d1ecf1; color: #0c5460; }
        
        .assignment-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .livespo-logo {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .livespo-logo h5 {
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="asset-card">
                    <!-- Asset Header -->
                    <div class="asset-header">
                        <div class="asset-code">{{ $asset->asset_code }}</div>
                        <div class="asset-name">{{ $asset->asset_name }}</div>
                    </div>
                    
                    <!-- Asset Information -->
                    <div class="info-section">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-tag"></i>
                                Danh mục
                            </div>
                            <div class="info-value">{{ $asset->category->name ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-industry"></i>
                                Thương hiệu
                            </div>
                            <div class="info-value">{{ $asset->brand ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-cube"></i>
                                Model
                            </div>
                            <div class="info-value">{{ $asset->model ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-barcode"></i>
                                Serial Number
                            </div>
                            <div class="info-value">{{ $asset->serial_number ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-info-circle"></i>
                                Trạng thái
                            </div>
                            <div class="info-value">
                                @switch($asset->status)
                                    @case('available')
                                        <span class="status-badge status-available">Khả dụng</span>
                                        @break
                                    @case('assigned')
                                        <span class="status-badge status-assigned">Đã cấp phát</span>
                                        @break
                                    @case('maintenance')
                                        <span class="status-badge status-maintenance">Bảo trì</span>
                                        @break
                                    @case('retired')
                                        <span class="status-badge status-retired">Ngừng sử dụng</span>
                                        @break
                                    @default
                                        <span class="status-badge">{{ $asset->status }}</span>
                                @endswitch
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Vị trí
                            </div>
                            <div class="info-value">{{ $asset->location ?? 'N/A' }}</div>
                        </div>
                        
                        @if($asset->purchase_date)
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar"></i>
                                Ngày mua
                            </div>
                            <div class="info-value">{{ $asset->purchase_date->format('d/m/Y') }}</div>
                        </div>
                        @endif
                        
                        @if($asset->warranty_expiry)
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-shield-alt"></i>
                                Bảo hành đến
                            </div>
                            <div class="info-value">
                                {{ $asset->warranty_expiry->format('d/m/Y') }}
                                @if($asset->warranty_expiry->isPast())
                                    <small class="text-danger">(Hết hạn)</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <!-- Assignment Information -->
                        @if($asset->currentAssignment)
                        <div class="assignment-info">
                            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Thông tin cấp phát</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Nhân viên:</strong><br>
                                    {{ $asset->currentAssignment->employee->full_name }}<br>
                                    <small class="text-muted">{{ $asset->currentAssignment->employee->employee_code }}</small>
                                </div>
                                <div class="col-6">
                                    <strong>Bộ phận:</strong><br>
                                    {{ $asset->currentAssignment->employee->department->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Ngày cấp phát:</strong><br>
                                    {{ $asset->currentAssignment->assigned_date->format('d/m/Y') }}
                                </div>
                                @if($asset->currentAssignment->expected_return_date)
                                <div class="col-6">
                                    <strong>Dự kiến thu hồi:</strong><br>
                                    {{ $asset->currentAssignment->expected_return_date->format('d/m/Y') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Livespo Logo -->
                    <div class="livespo-logo">
                        <h5><i class="fas fa-boxes me-2"></i>LIVESPO ASSET MANAGEMENT</h5>
                        <p class="text-muted mb-0">Hệ thống quản lý tài sản nội bộ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>