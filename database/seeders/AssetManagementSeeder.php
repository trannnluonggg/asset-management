<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;
use App\Models\AssetCategory;
use App\Models\Asset;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

class AssetManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo phòng ban
        $departments = [
            ['code' => 'IT', 'name' => 'Phòng Công nghệ thông tin', 'description' => 'Quản lý hệ thống IT'],
            ['code' => 'HR', 'name' => 'Phòng Nhân sự', 'description' => 'Quản lý nhân sự'],
            ['code' => 'ACC', 'name' => 'Phòng Kế toán', 'description' => 'Quản lý tài chính'],
            ['code' => 'MKT', 'name' => 'Phòng Marketing', 'description' => 'Marketing và bán hàng'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Tạo nhân viên
        $employees = [
            [
                'employee_code' => 'EMP001',
                'full_name' => 'Nguyễn Văn Admin',
                'email' => 'admin@livespo.com',
                'phone' => '0901234567',
                'department_id' => 1,
                'position' => 'Quản trị viên hệ thống',
                'hire_date' => '2023-01-01',
                'status' => 'active'
            ],
            [
                'employee_code' => 'EMP002',
                'full_name' => 'Trần Thị HR',
                'email' => 'hr@livespo.com',
                'phone' => '0901234568',
                'department_id' => 2,
                'position' => 'Trưởng phòng nhân sự',
                'hire_date' => '2023-01-15',
                'status' => 'active'
            ],
            [
                'employee_code' => 'EMP003',
                'full_name' => 'Lê Văn User',
                'email' => 'user@livespo.com',
                'phone' => '0901234569',
                'department_id' => 3,
                'position' => 'Nhân viên kế toán',
                'hire_date' => '2023-02-01',
                'status' => 'active'
            ],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }

        // Tạo user accounts
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'employee_id' => 1,
                'role' => 'admin',
                'is_active' => true
            ],
            [
                'username' => 'hr',
                'password' => Hash::make('hr123'),
                'employee_id' => 2,
                'role' => 'hr',
                'is_active' => true
            ],
            [
                'username' => 'user',
                'password' => Hash::make('user123'),
                'employee_id' => 3,
                'role' => 'user',
                'is_active' => true
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Tạo danh mục tài sản
        $categories = [
            ['code' => 'LAPTOP', 'name' => 'Laptop', 'description' => 'Máy tính xách tay', 'depreciation_years' => 3],
            ['code' => 'DESKTOP', 'name' => 'Desktop', 'description' => 'Máy tính để bàn', 'depreciation_years' => 4],
            ['code' => 'PHONE', 'name' => 'Điện thoại', 'description' => 'Điện thoại di động', 'depreciation_years' => 2],
            ['code' => 'PRINTER', 'name' => 'Máy in', 'description' => 'Thiết bị in ấn', 'depreciation_years' => 5],
            ['code' => 'MONITOR', 'name' => 'Màn hình', 'description' => 'Màn hình máy tính', 'depreciation_years' => 5],
        ];

        foreach ($categories as $cat) {
            AssetCategory::create($cat);
        }

        // Tạo tài sản mẫu
        $assets = [
            [
                'asset_code' => 'LT001',
                'qr_code' => 'QR_LT001_' . time(),
                'asset_name' => 'Laptop Dell Inspiron 15',
                'category_id' => 1,
                'brand' => 'Dell',
                'model' => 'Inspiron 15 3000',
                'serial_number' => 'DL123456789',
                'purchase_date' => '2023-01-15',
                'purchase_price' => 15000000,
                'warranty_expiry' => '2025-01-15',
                'condition_status' => 'good',
                'location' => 'Phòng IT',
                'status' => 'available'
            ],
            [
                'asset_code' => 'DT001',
                'qr_code' => 'QR_DT001_' . (time() + 1),
                'asset_name' => 'Desktop HP EliteDesk',
                'category_id' => 2,
                'brand' => 'HP',
                'model' => 'EliteDesk 800 G5',
                'serial_number' => 'HP987654321',
                'purchase_date' => '2023-02-01',
                'purchase_price' => 12000000,
                'warranty_expiry' => '2026-02-01',
                'condition_status' => 'new',
                'location' => 'Phòng Kế toán',
                'status' => 'available'
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }

        // Tạo system settings
        $settings = [
            ['setting_key' => 'company_name', 'setting_value' => 'Livespo Company', 'setting_type' => 'string', 'description' => 'Tên công ty'],
            ['setting_key' => 'company_address', 'setting_value' => 'Hà Nội, Việt Nam', 'setting_type' => 'string', 'description' => 'Địa chỉ công ty'],
            ['setting_key' => 'qr_code_prefix', 'setting_value' => 'LIVESPO', 'setting_type' => 'string', 'description' => 'Tiền tố mã QR'],
            ['setting_key' => 'auto_assign_qr', 'setting_value' => 'true', 'setting_type' => 'boolean', 'description' => 'Tự động tạo mã QR'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::create($setting);
        }
    }
}
