@extends('layouts.app')

@section('title', 'Cài đặt hệ thống')

@section('page-title', 'Cài đặt hệ thống')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Cài đặt hệ thống
                </h5>
                <div>
                    <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#resetModal">
                        <i class="fas fa-undo me-1"></i>Khôi phục mặc định
                    </button>
                    <button type="submit" form="settingsForm" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Lưu cài đặt
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="settingsForm" method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Settings Tabs -->
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        @foreach($settings as $groupKey => $group)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                    id="{{ $groupKey }}-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#{{ $groupKey }}" 
                                    type="button" 
                                    role="tab">
                                <i class="{{ $group['icon'] }} me-2"></i>
                                {{ $group['title'] }}
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-4" id="settingsTabsContent">
                        @foreach($settings as $groupKey => $group)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                             id="{{ $groupKey }}" 
                             role="tabpanel">
                            
                            <h6 class="mb-4">
                                <i class="{{ $group['icon'] }} me-2"></i>
                                {{ $group['title'] }}
                            </h6>

                            <div class="row">
                                @foreach($group['settings'] as $settingKey => $setting)
                                <div class="col-md-6 mb-4">
                                    <div class="card border-light">
                                        <div class="card-body">
                                            <label for="{{ $settingKey }}" class="form-label fw-bold">
                                                {{ $setting['label'] }}
                                            </label>
                                            
                                            @if($setting['type'] === 'text')
                                                <input type="text" 
                                                       class="form-control @error('settings.'.$settingKey) is-invalid @enderror" 
                                                       id="{{ $settingKey }}" 
                                                       name="settings[{{ $settingKey }}]" 
                                                       value="{{ old('settings.'.$settingKey, $setting['value']) }}">
                                            
                                            @elseif($setting['type'] === 'email')
                                                <input type="email" 
                                                       class="form-control @error('settings.'.$settingKey) is-invalid @enderror" 
                                                       id="{{ $settingKey }}" 
                                                       name="settings[{{ $settingKey }}]" 
                                                       value="{{ old('settings.'.$settingKey, $setting['value']) }}">
                                            
                                            @elseif($setting['type'] === 'url')
                                                <input type="url" 
                                                       class="form-control @error('settings.'.$settingKey) is-invalid @enderror" 
                                                       id="{{ $settingKey }}" 
                                                       name="settings[{{ $settingKey }}]" 
                                                       value="{{ old('settings.'.$settingKey, $setting['value']) }}">
                                            
                                            @elseif($setting['type'] === 'number')
                                                <input type="number" 
                                                       class="form-control @error('settings.'.$settingKey) is-invalid @enderror" 
                                                       id="{{ $settingKey }}" 
                                                       name="settings[{{ $settingKey }}]" 
                                                       value="{{ old('settings.'.$settingKey, $setting['value']) }}"
                                                       min="0">
                                            
                                            @elseif($setting['type'] === 'textarea')
                                                <textarea class="form-control @error('settings.'.$settingKey) is-invalid @enderror" 
                                                          id="{{ $settingKey }}" 
                                                          name="settings[{{ $settingKey }}]" 
                                                          rows="3">{{ old('settings.'.$settingKey, $setting['value']) }}</textarea>
                                            
                                            @elseif($setting['type'] === 'select')
                                                <select class="form-select @error('settings.'.$settingKey) is-invalid @enderror" 
                                                        id="{{ $settingKey }}" 
                                                        name="settings[{{ $settingKey }}]">
                                                    @foreach($setting['options'] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" 
                                                                {{ old('settings.'.$settingKey, $setting['value']) == $optionValue ? 'selected' : '' }}>
                                                            {{ $optionLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            
                                            @elseif($setting['type'] === 'checkbox')
                                                <div class="form-check">
                                                    <!-- Hidden input to ensure unchecked checkboxes send '0' -->
                                                    <input type="hidden" name="settings[{{ $settingKey }}]" value="0">
                                                    <input class="form-check-input @error('settings.'.$settingKey) is-invalid @enderror" 
                                                           type="checkbox" 
                                                           id="{{ $settingKey }}" 
                                                           name="settings[{{ $settingKey }}]" 
                                                           value="1"
                                                           {{ old('settings.'.$settingKey, $setting['value']) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $settingKey }}">
                                                        Bật
                                                    </label>
                                                </div>
                                            @endif

                                            @error('settings.'.$settingKey)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            
                                            @if(isset($setting['description']))
                                                <div class="form-text">{{ $setting['description'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Save Button -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu tất cả cài đặt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Xác nhận khôi phục
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn khôi phục tất cả cài đặt về giá trị mặc định?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Cảnh báo:</strong> Tất cả cài đặt hiện tại sẽ bị ghi đè và không thể hoàn tác.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <form method="POST" action="{{ route('settings.reset') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-2"></i>Khôi phục mặc định
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save indication
    const form = document.getElementById('settingsForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    let hasChanges = false;

    inputs.forEach(input => {
        input.addEventListener('change', function() {
            hasChanges = true;
            updateSaveButton();
        });
    });

    function updateSaveButton() {
        const saveButtons = document.querySelectorAll('button[type="submit"]');
        saveButtons.forEach(button => {
            if (hasChanges) {
                button.classList.add('btn-warning');
                button.classList.remove('btn-primary');
                button.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Có thay đổi - Lưu ngay';
            }
        });
    }

    // Form submission
    form.addEventListener('submit', function() {
        hasChanges = false;
        const saveButtons = document.querySelectorAll('button[type="submit"]');
        saveButtons.forEach(button => {
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...';
            button.disabled = true;
        });
    });

    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
        }
    });

    // QR Code preview
    const qrSizeInput = document.getElementById('qr_size');
    const qrMarginInput = document.getElementById('qr_margin');
    
    if (qrSizeInput && qrMarginInput) {
        function updateQRPreview() {
            // You can add QR code preview functionality here
            console.log('QR Size:', qrSizeInput.value, 'Margin:', qrMarginInput.value);
        }
        
        qrSizeInput.addEventListener('change', updateQRPreview);
        qrMarginInput.addEventListener('change', updateQRPreview);
    }
});
</script>
@endsection