<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_without_customers_permission_cannot_open_customer_records(): void
    {
        $role = Role::create(['name' => 'limited_test', 'display_name' => 'ทดสอบสิทธิ์']);
        $admin = Admin::create(['name' => 'ผู้ดูแลทดสอบ', 'email' => uniqid('limited-', true).'@example.test', 'password' => 'test-password', 'role_id' => $role->id, 'is_active' => true]);

        $this->actingAs($admin, 'admin')->get(route('admin.customers.index'))->assertForbidden();
    }

    public function test_admin_with_customers_permission_can_open_customer_records(): void
    {
        $role = Role::create(['name' => 'customer_manager_test', 'display_name' => 'ผู้จัดการผู้รับบริการ']);
        $role->permissions()->attach(Permission::where('name', 'customers.view')->value('id'));
        $admin = Admin::create(['name' => 'ผู้ดูแลทดสอบ', 'email' => uniqid('manager-', true).'@example.test', 'password' => 'test-password', 'role_id' => $role->id, 'is_active' => true]);

        $this->actingAs($admin, 'admin')->get(route('admin.customers.index'))->assertOk();
    }

    public function test_super_admin_can_view_all_user_types_on_unified_management_page(): void
    {
        $customer = Customer::create([
            'hn' => 'HN-TEST-001',
            'first_name' => 'รวมผู้รับบริการ',
            'last_name' => 'ทดสอบ',
            'phone' => '0812345678',
            'email' => 'combined-customer@example.test',
            'gender' => 'other',
            'is_active' => true,
        ]);
        $admin = Admin::create([
            'name' => 'รวมเจ้าหน้าที่ทดสอบ',
            'email' => 'combined-admin@example.test',
            'password' => 'test-password',
            'role_id' => Role::where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);
        $superAdmin = Admin::where('email', 'admin@wlu-clinic.local')->firstOrFail();

        $this->actingAs($superAdmin, 'admin')
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($customer->full_name)
            ->assertSee($admin->name)
            ->assertSee('จัดการผู้ใช้');
    }

    public function test_staff_can_view_customers_without_exposing_admin_accounts(): void
    {
        $customer = Customer::create([
            'hn' => 'HN-TEST-002',
            'first_name' => 'เจ้าหน้าที่เห็นได้',
            'last_name' => 'ผู้รับบริการ',
            'phone' => '0823456789',
            'email' => 'staff-visible-customer@example.test',
            'gender' => 'other',
            'is_active' => true,
        ]);
        $hiddenAdmin = Admin::create([
            'name' => 'บัญชีที่ไม่ควรเห็น',
            'email' => 'hidden-admin@example.test',
            'password' => 'test-password',
            'role_id' => Role::where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);
        $staff = Admin::where('email', 'staff@wlu-clinic.local')->firstOrFail();

        $this->actingAs($staff, 'admin')
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($customer->full_name)
            ->assertDontSee($hiddenAdmin->name);
    }

    public function test_active_admin_visiting_login_is_sent_to_admin_dashboard_not_public_home(): void
    {
        $admin = Admin::where('email', 'admin@wlu-clinic.local')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_can_open_admin_login_page(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('ระบบเจ้าหน้าที่');
    }

    public function test_active_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@wlu-clinic.local',
            'password' => 'Admin@1234',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(auth('admin')->check());
        $this->assertSame('admin@wlu-clinic.local', auth('admin')->user()->email);
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = Admin::where('email', 'admin@wlu-clinic.local')->firstOrFail();
        $admin->update(['is_active' => false]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@wlu-clinic.local',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(auth('admin')->check());
    }
}
