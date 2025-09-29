<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Middleware will be handled in routes or via attributes
    }
    
    private function checkAdminAccess()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới có quyền truy cập cài đặt hệ thống.');
        }
    }

    public function index()
    {
        $this->checkAdminAccess();
        
        $settings = $this->getSettingsGrouped();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get all possible setting keys from the settings structure
        $allSettings = $this->getSettingsGrouped();
        $allSettingKeys = [];
        foreach ($allSettings as $group) {
            $allSettingKeys = array_merge($allSettingKeys, array_keys($group['settings']));
        }
        
        // Process each setting key
        foreach ($allSettingKeys as $key) {
            $value = $request->input("settings.{$key}");
            
            // Handle null/empty values - convert to empty string to avoid database constraint issues
            if ($value === null) {
                $value = '';
            }
            
            SystemSetting::setValue($key, $value);
        }

        // Clear settings cache
        Cache::forget('system_settings');

        return redirect()->route('settings.index')
            ->with('success', 'Cài đặt hệ thống đã được cập nhật thành công.');
    }

    public function reset()
    {
        $this->checkAdminAccess();
        
        $this->createDefaultSettings();
        
        // Clear settings cache
        Cache::forget('system_settings');

        return redirect()->route('settings.index')
            ->with('success', 'Đã khôi phục cài đặt mặc định.');
    }

    private function getSettingsGrouped()
    {
        return [
            'company' => [
                'title' => 'Thông tin công ty',
                'icon' => 'fas fa-building',
                'settings' => [
                    'company_name' => [
                        'label' => 'Tên công ty',
                        'type' => 'text',
                        'value' => SystemSetting::getValue('company_name', 'Livespo Vietnam Co., Ltd'),
                        'description' => 'Tên đầy đủ của công ty'
                    ],
                    'company_address' => [
                        'label' => 'Địa chỉ công ty',
                        'type' => 'textarea',
                        'value' => SystemSetting::getValue('company_address', ''),
                        'description' => 'Địa chỉ trụ sở chính của công ty'
                    ],
                    'company_phone' => [
                        'label' => 'Số điện thoại',
                        'type' => 'text',
                        'value' => SystemSetting::getValue('company_phone', ''),
                        'description' => 'Số điện thoại liên hệ chính'
                    ],
                    'company_email' => [
                        'label' => 'Email công ty',
                        'type' => 'email',
                        'value' => SystemSetting::getValue('company_email', 'info@livespo.vn'),
                        'description' => 'Email liên hệ chính của công ty'
                    ],
                    'company_website' => [
                        'label' => 'Website',
                        'type' => 'url',
                        'value' => SystemSetting::getValue('company_website', 'https://livespo.vn'),
                        'description' => 'Website chính thức của công ty'
                    ]
                ]
            ],
            'qr' => [
                'title' => 'Cài đặt QR Code',
                'icon' => 'fas fa-qrcode',
                'settings' => [
                    'qr_base_url' => [
                        'label' => 'URL cơ sở cho QR Code',
                        'type' => 'url',
                        'value' => SystemSetting::getValue('qr_base_url', 'https://qlts.livespo.vn'),
                        'description' => 'URL cơ sở sẽ được sử dụng trong QR code'
                    ],
                    'qr_size' => [
                        'label' => 'Kích thước QR Code (px)',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('qr_size', '200'),
                        'description' => 'Kích thước QR code tính bằng pixel'
                    ],
                    'qr_margin' => [
                        'label' => 'Lề QR Code',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('qr_margin', '2'),
                        'description' => 'Kích thước lề xung quanh QR code'
                    ],
                    'qr_error_correction' => [
                        'label' => 'Mức độ sửa lỗi',
                        'type' => 'select',
                        'value' => SystemSetting::getValue('qr_error_correction', 'M'),
                        'options' => [
                            'L' => 'Thấp (~7%)',
                            'M' => 'Trung bình (~15%)',
                            'Q' => 'Cao (~25%)',
                            'H' => 'Rất cao (~30%)'
                        ],
                        'description' => 'Mức độ khả năng sửa lỗi của QR code'
                    ]
                ]
            ],
            'notification' => [
                'title' => 'Cài đặt thông báo',
                'icon' => 'fas fa-bell',
                'settings' => [
                    'notification_email_enabled' => [
                        'label' => 'Bật thông báo email',
                        'type' => 'checkbox',
                        'value' => SystemSetting::getValue('notification_email_enabled', '1'),
                        'description' => 'Gửi thông báo qua email cho các sự kiện quan trọng'
                    ],
                    'notification_assignment_expiry_days' => [
                        'label' => 'Cảnh báo hết hạn cấp phát (ngày)',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('notification_assignment_expiry_days', '30'),
                        'description' => 'Số ngày trước khi hết hạn cấp phát để gửi cảnh báo'
                    ],
                    'notification_incident_auto_assign' => [
                        'label' => 'Tự động gán sự cố cho HR',
                        'type' => 'checkbox',
                        'value' => SystemSetting::getValue('notification_incident_auto_assign', '1'),
                        'description' => 'Tự động gán sự cố mới cho nhân viên HR'
                    ]
                ]
            ],
            'system' => [
                'title' => 'Cài đặt hệ thống',
                'icon' => 'fas fa-cogs',
                'settings' => [
                    'system_maintenance_mode' => [
                        'label' => 'Chế độ bảo trì',
                        'type' => 'checkbox',
                        'value' => SystemSetting::getValue('system_maintenance_mode', '0'),
                        'description' => 'Bật chế độ bảo trì hệ thống (chỉ admin có thể truy cập)'
                    ],
                    'system_pagination_limit' => [
                        'label' => 'Số bản ghi trên mỗi trang',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('system_pagination_limit', '15'),
                        'description' => 'Số lượng bản ghi hiển thị trên mỗi trang danh sách'
                    ],
                    'system_session_timeout' => [
                        'label' => 'Thời gian hết hạn phiên (phút)',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('system_session_timeout', '120'),
                        'description' => 'Thời gian tự động đăng xuất khi không hoạt động'
                    ],
                    'system_backup_enabled' => [
                        'label' => 'Bật sao lưu tự động',
                        'type' => 'checkbox',
                        'value' => SystemSetting::getValue('system_backup_enabled', '1'),
                        'description' => 'Tự động sao lưu dữ liệu hệ thống'
                    ],
                    'system_log_level' => [
                        'label' => 'Mức độ ghi log',
                        'type' => 'select',
                        'value' => SystemSetting::getValue('system_log_level', 'info'),
                        'options' => [
                            'debug' => 'Debug',
                            'info' => 'Info',
                            'warning' => 'Warning',
                            'error' => 'Error'
                        ],
                        'description' => 'Mức độ chi tiết của log hệ thống'
                    ]
                ]
            ],
            'security' => [
                'title' => 'Cài đặt bảo mật',
                'icon' => 'fas fa-shield-alt',
                'settings' => [
                    'security_password_min_length' => [
                        'label' => 'Độ dài mật khẩu tối thiểu',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('security_password_min_length', '8'),
                        'description' => 'Số ký tự tối thiểu cho mật khẩu'
                    ],
                    'security_password_require_special' => [
                        'label' => 'Yêu cầu ký tự đặc biệt',
                        'type' => 'checkbox',
                        'value' => SystemSetting::getValue('security_password_require_special', '1'),
                        'description' => 'Mật khẩu phải chứa ít nhất một ký tự đặc biệt'
                    ],
                    'security_login_attempts' => [
                        'label' => 'Số lần đăng nhập sai tối đa',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('security_login_attempts', '5'),
                        'description' => 'Số lần đăng nhập sai trước khi khóa tài khoản'
                    ],
                    'security_lockout_duration' => [
                        'label' => 'Thời gian khóa tài khoản (phút)',
                        'type' => 'number',
                        'value' => SystemSetting::getValue('security_lockout_duration', '15'),
                        'description' => 'Thời gian khóa tài khoản sau khi đăng nhập sai quá nhiều lần'
                    ]
                ]
            ]
        ];
    }

    private function createDefaultSettings()
    {
        $defaultSettings = [
            // Company settings
            'company_name' => 'Livespo Vietnam Co., Ltd',
            'company_address' => '',
            'company_phone' => '',
            'company_email' => 'info@livespo.vn',
            'company_website' => 'https://livespo.vn',
            
            // QR settings
            'qr_base_url' => 'https://qlts.livespo.vn',
            'qr_size' => '200',
            'qr_margin' => '2',
            'qr_error_correction' => 'M',
            
            // Notification settings
            'notification_email_enabled' => '1',
            'notification_assignment_expiry_days' => '30',
            'notification_incident_auto_assign' => '1',
            
            // System settings
            'system_maintenance_mode' => '0',
            'system_pagination_limit' => '15',
            'system_session_timeout' => '120',
            'system_backup_enabled' => '1',
            'system_log_level' => 'info',
            
            // Security settings
            'security_password_min_length' => '8',
            'security_password_require_special' => '1',
            'security_login_attempts' => '5',
            'security_lockout_duration' => '15'
        ];

        foreach ($defaultSettings as $key => $value) {
            SystemSetting::setValue($key, $value);
        }
    }
}