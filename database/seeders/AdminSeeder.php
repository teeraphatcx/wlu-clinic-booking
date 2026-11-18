<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // Super Admin (as required)
        Admin::updateOrCreate(
            ['email' => 'admin@wlu-clinic.local'],
            [
                'name' => 'ผู้ดูแลระบบ',
                'password' => Hash::make('Admin@1234'),
                'role_id' => $superAdminRole->id,
                'is_active' => true,
                'last_login_at' => null,
            ]
        );

        // General Admin
        Admin::updateOrCreate(
            ['email' => 'manager@wlu-clinic.local'],
            [
                'name' => 'ผู้จัดการคลินิก',
                'password' => Hash::make('Admin@1234'),
                'role_id' => $adminRole ? $adminRole->id : $superAdminRole->id,
                'is_active' => true,
            ]
        );

        // Staff
        Admin::updateOrCreate(
            ['email' => 'staff@wlu-clinic.local'],
            [
                'name' => 'เจ้าหน้าที่เคาน์เตอร์',
                'password' => Hash::make('Admin@1234'),
                'role_id' => $staffRole ? $staffRole->id : $superAdminRole->id,
                'is_active' => true,
            ]
        );
    }
}
