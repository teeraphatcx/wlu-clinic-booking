<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Bookings
            ['name' => 'bookings.view', 'display_name' => 'ดูรายการจองคิว', 'group' => 'bookings'],
            ['name' => 'bookings.create', 'display_name' => 'สร้างรายการจองคิว', 'group' => 'bookings'],
            ['name' => 'bookings.edit', 'display_name' => 'แก้ไขรายการจองคิว', 'group' => 'bookings'],
            ['name' => 'bookings.cancel', 'display_name' => 'ยกเลิกรายการจองคิว', 'group' => 'bookings'],
            ['name' => 'bookings.status', 'display_name' => 'เปลี่ยนสถานะการจอง', 'group' => 'bookings'],
            ['name' => 'bookings.export', 'display_name' => 'ส่งออกข้อมูลการจอง', 'group' => 'bookings'],

            // Customers
            ['name' => 'customers.view', 'display_name' => 'ดูข้อมูลผู้รับบริการ', 'group' => 'customers'],
            ['name' => 'customers.create', 'display_name' => 'เพิ่มผู้รับบริการ', 'group' => 'customers'],
            ['name' => 'customers.edit', 'display_name' => 'แก้ไขข้อมูลผู้รับบริการ', 'group' => 'customers'],
            ['name' => 'customers.delete', 'display_name' => 'ลบข้อมูลผู้รับบริการ', 'group' => 'customers'],
            ['name' => 'customers.export', 'display_name' => 'ส่งออกข้อมูลผู้รับบริการ', 'group' => 'customers'],

            // Services
            ['name' => 'services.view', 'display_name' => 'ดูรายการหัตถการ', 'group' => 'services'],
            ['name' => 'services.create', 'display_name' => 'เพิ่มหัตถการใหม่', 'group' => 'services'],
            ['name' => 'services.edit', 'display_name' => 'แก้ไขหัตถการ', 'group' => 'services'],
            ['name' => 'services.delete', 'display_name' => 'ลบหัตถการ', 'group' => 'services'],

            // Therapists
            ['name' => 'therapists.view', 'display_name' => 'ดูข้อมูลหมอนวด/แพทย์แผนไทย', 'group' => 'therapists'],
            ['name' => 'therapists.create', 'display_name' => 'เพิ่มข้อมูลหมอนวด', 'group' => 'therapists'],
            ['name' => 'therapists.edit', 'display_name' => 'แก้ไขข้อมูลหมอนวด', 'group' => 'therapists'],
            ['name' => 'therapists.delete', 'display_name' => 'ลบข้อมูลหมอนวด', 'group' => 'therapists'],
            ['name' => 'therapists.schedule', 'display_name' => 'จัดการตารางเวลาหมอนวด', 'group' => 'therapists'],

            // Schedules
            ['name' => 'schedules.view', 'display_name' => 'ดูตารางคลินิกและวันหยุด', 'group' => 'schedules'],
            ['name' => 'schedules.edit', 'display_name' => 'จัดการตารางคลินิกและวันหยุด', 'group' => 'schedules'],

            // Admins
            ['name' => 'admins.view', 'display_name' => 'ดูรายชื่อเจ้าหน้าที่', 'group' => 'admins'],
            ['name' => 'admins.create', 'display_name' => 'เพิ่มเจ้าหน้าที่', 'group' => 'admins'],
            ['name' => 'admins.edit', 'display_name' => 'แก้ไขข้อมูลเจ้าหน้าที่', 'group' => 'admins'],
            ['name' => 'admins.delete', 'display_name' => 'ลบเจ้าหน้าที่', 'group' => 'admins'],

            // Settings
            ['name' => 'settings.view', 'display_name' => 'ดูการตั้งค่าระบบ', 'group' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'แก้ไขการตั้งค่าระบบ', 'group' => 'settings'],

            // Contents
            ['name' => 'contents.view', 'display_name' => 'ดูเนื้อหาประชาสัมพันธ์', 'group' => 'contents'],
            ['name' => 'contents.create', 'display_name' => 'สร้างเนื้อหาประชาสัมพันธ์', 'group' => 'contents'],
            ['name' => 'contents.edit', 'display_name' => 'แก้ไขเนื้อหาประชาสัมพันธ์', 'group' => 'contents'],
            ['name' => 'contents.delete', 'display_name' => 'ลบเนื้อหาประชาสัมพันธ์', 'group' => 'contents'],

            // Reports
            ['name' => 'reports.view', 'display_name' => 'ดูรายงานสถิติ', 'group' => 'reports'],
            ['name' => 'reports.export', 'display_name' => 'ส่งออกรายงาน', 'group' => 'reports'],

            // Audit Logs
            ['name' => 'audit_logs.view', 'display_name' => 'ดูประวัติการใช้งาน (Audit Logs)', 'group' => 'audit_logs'],
        ];

        $permissionModels = [];
        foreach ($permissions as $p) {
            $permissionModels[$p['name']] = Permission::updateOrCreate(
                ['name' => $p['name']],
                [
                    'display_name' => $p['display_name'],
                    'group' => $p['group'],
                    'description' => "สิทธิ์{$p['display_name']}",
                ]
            );
        }

        // Roles
        $superAdminRole = Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'ผู้ดูแลระบบสูงสุด (Super Admin)',
                'description' => 'มีสิทธิ์เข้าถึงและจัดการทุกส่วนของระบบ',
            ]
        );

        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'ผู้ดูแลระบบ (Admin)',
                'description' => 'จัดการการจอง หัตถการ หมอนวด ตารางเวลา และรายงาน',
            ]
        );

        $staffRole = Role::updateOrCreate(
            ['name' => 'staff'],
            [
                'display_name' => 'เจ้าหน้าที่ประจำคลินิก (Staff)',
                'description' => 'จัดการการจองคิวหน้าเคาน์เตอร์ และดูข้อมูลผู้รับบริการ',
            ]
        );

        // Assign permissions
        // super_admin gets all permissions
        $allPermissionIds = collect($permissionModels)->pluck('id');
        $superAdminRole->permissions()->sync($allPermissionIds);

        // admin gets most permissions (except admin user management & audit logs deletion)
        $adminPermissions = collect($permissionModels)
            ->filter(fn ($p) => ! in_array($p->group, ['audit_logs']))
            ->pluck('id');
        $adminRole->permissions()->sync($adminPermissions);

        // staff gets counter booking & view permissions
        $staffPermissionNames = [
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.status',
            'customers.view', 'customers.create', 'customers.edit',
            'services.view', 'therapists.view', 'schedules.view', 'contents.view',
        ];
        $staffPermissions = collect($permissionModels)
            ->filter(fn ($p) => in_array($p->name, $staffPermissionNames))
            ->pluck('id');
        $staffRole->permissions()->sync($staffPermissions);
    }
}
