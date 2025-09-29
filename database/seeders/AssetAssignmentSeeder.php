<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssetAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available assets and employees
        $assets = Asset::where('status', 'available')->get();
        $employees = Employee::where('status', 'active')->get();
        $adminUser = User::where('role', 'admin')->first();

        if ($assets->isEmpty() || $employees->isEmpty() || !$adminUser) {
            $this->command->info('Không có đủ dữ liệu để tạo assignment. Cần có assets, employees và admin user.');
            return;
        }

        $assignments = [
            // Active assignments
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(10),
                'expected_return_date' => Carbon::now()->addDays(20), // Sắp hết hạn
                'status' => 'active',
                'assignment_notes' => 'Cấp phát laptop cho công việc hàng ngày'
            ],
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(5),
                'expected_return_date' => Carbon::now()->addDays(5), // Sắp hết hạn
                'status' => 'active',
                'assignment_notes' => 'Cấp phát điện thoại công ty'
            ],
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(15),
                'expected_return_date' => Carbon::now()->addDays(45),
                'status' => 'active',
                'assignment_notes' => 'Cấp phát máy tính để bàn cho phòng kế toán'
            ],
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(3),
                'expected_return_date' => Carbon::now()->addDays(2), // Sắp hết hạn
                'status' => 'active',
                'assignment_notes' => 'Cấp phát tablet cho presentation'
            ],
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(20),
                'expected_return_date' => Carbon::now()->addDays(10), // Sắp hết hạn
                'status' => 'active',
                'assignment_notes' => 'Cấp phát máy in cho bộ phận marketing'
            ],
            
            // Returned assignments
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(60),
                'expected_return_date' => Carbon::now()->subDays(30),
                'actual_return_date' => Carbon::now()->subDays(25),
                'status' => 'returned',
                'return_condition' => 'good',
                'assignment_notes' => 'Cấp phát laptop tạm thời',
                'return_notes' => 'Trả lại đúng hạn, tình trạng tốt'
            ],
            [
                'asset' => $assets->random(),
                'employee' => $employees->random(),
                'assigned_date' => Carbon::now()->subDays(45),
                'expected_return_date' => Carbon::now()->subDays(15),
                'actual_return_date' => Carbon::now()->subDays(10),
                'status' => 'returned',
                'return_condition' => 'fair',
                'assignment_notes' => 'Cấp phát máy tính cho dự án',
                'return_notes' => 'Trả lại muộn 5 ngày, có một số vết xước nhỏ'
            ]
        ];

        foreach ($assignments as $assignmentData) {
            // Check if asset is still available for assignment
            $asset = $assignmentData['asset'];
            if ($asset->status !== 'available' && $assignmentData['status'] === 'active') {
                continue; // Skip if asset is not available
            }

            $assignment = AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $assignmentData['employee']->id,
                'assigned_by' => $adminUser->id,
                'assigned_date' => $assignmentData['assigned_date'],
                'expected_return_date' => $assignmentData['expected_return_date'],
                'actual_return_date' => $assignmentData['actual_return_date'] ?? null,
                'status' => $assignmentData['status'],
                'assignment_notes' => $assignmentData['assignment_notes'],
                'return_condition' => $assignmentData['return_condition'] ?? null,
                'return_notes' => $assignmentData['return_notes'] ?? null,
            ]);

            // Update asset status
            if ($assignmentData['status'] === 'active') {
                $asset->update(['status' => 'assigned']);
            } elseif ($assignmentData['status'] === 'returned') {
                $asset->update([
                    'status' => 'available',
                    'condition_status' => $assignmentData['return_condition']
                ]);
            }

            // Create history record
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action_type' => $assignmentData['status'] === 'returned' ? 'returned' : 'assigned',
                'performed_by' => $adminUser->id,
                'old_value' => json_encode(['status' => 'available']),
                'new_value' => json_encode([
                    'status' => $assignmentData['status'] === 'active' ? 'assigned' : 'available',
                    'employee_id' => $assignmentData['employee']->id,
                    'assignment_id' => $assignment->id
                ]),
                'notes' => $assignmentData['status'] === 'returned' 
                    ? 'Tài sản được thu hồi từ nhân viên' 
                    : 'Tài sản được cấp phát cho nhân viên',
                'created_at' => $assignmentData['status'] === 'returned' 
                    ? $assignmentData['actual_return_date'] 
                    : $assignmentData['assigned_date']
            ]);
        }

        $this->command->info('Đã tạo ' . count($assignments) . ' assignment records.');
    }
}
