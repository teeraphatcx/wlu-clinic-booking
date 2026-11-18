<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use App\Models\Content;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Therapist;
use App\Models\TherapistDayOff;
use App\Models\TherapistSchedule;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function users(Request $request): View
    {
        $admin = auth('admin')->user();
        $canViewCustomers = $admin?->hasPermission('customers.view') ?? false;
        $canViewAdmins = $admin?->hasPermission('admins.view') ?? false;
        $canManageCustomers = $admin?->hasPermission('customers.edit') ?? false;
        $canManageAdmins = $admin?->hasPermission('admins.edit') ?? false;
        $type = in_array($request->input('type'), ['customer', 'admin'], true) ? $request->input('type') : 'all';
        $state = in_array($request->input('state'), ['0', '1'], true) ? $request->input('state') : null;
        $search = trim((string) $request->input('search', ''));
        $queries = [];

        if ($canViewCustomers && $type !== 'admin') {
            $queries[] = Customer::query()
                ->select([
                    'customers.id',
                    DB::raw("'customer' as user_type"),
                    'customers.first_name',
                    'customers.last_name',
                    DB::raw('NULL as admin_name'),
                    'customers.email',
                    'customers.phone',
                    'customers.hn as reference_value',
                    'customers.is_active',
                    'customers.created_at',
                    DB::raw('NULL as role_display'),
                    DB::raw('NULL as last_login_at'),
                ])
                ->selectSub(
                    Booking::query()
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('bookings.customer_id', 'customers.id'),
                    'activity_count'
                )
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $customerQuery) use ($search): void {
                        $customerQuery->where('customers.first_name', 'like', "%{$search}%")
                            ->orWhere('customers.last_name', 'like', "%{$search}%")
                            ->orWhere('customers.phone', 'like', "%{$search}%")
                            ->orWhere('customers.email', 'like', "%{$search}%")
                            ->orWhere('customers.hn', 'like', "%{$search}%");
                    });
                })
                ->when($state !== null, fn (Builder $query): Builder => $query->where('customers.is_active', $state === '1'))
                ->toBase();
        }

        if ($canViewAdmins && $type !== 'customer') {
            $queries[] = Admin::query()
                ->leftJoin('roles', 'roles.id', '=', 'admins.role_id')
                ->select([
                    'admins.id',
                    DB::raw("'admin' as user_type"),
                    DB::raw('NULL as first_name'),
                    DB::raw('NULL as last_name'),
                    'admins.name as admin_name',
                    'admins.email',
                    DB::raw('NULL as phone'),
                    DB::raw('NULL as reference_value'),
                    'admins.is_active',
                    'admins.created_at',
                    'roles.display_name as role_display',
                    'admins.last_login_at',
                    DB::raw('NULL as activity_count'),
                ])
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $adminQuery) use ($search): void {
                        $adminQuery->where('admins.name', 'like', "%{$search}%")
                            ->orWhere('admins.email', 'like', "%{$search}%");
                    });
                })
                ->when($state !== null, fn (Builder $query): Builder => $query->where('admins.is_active', $state === '1'))
                ->toBase();
        }

        $visibleUsers = array_shift($queries);
        foreach ($queries as $query) {
            $visibleUsers->unionAll($query);
        }

        $usersQuery = DB::query()->fromSub($visibleUsers, 'visible_users');
        $perPage = 25;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $totalUsers = (clone $usersQuery)->count();
        $userRows = $usersQuery
            ->orderByDesc('created_at')
            ->forPage($currentPage, $perPage)
            ->get();
        $users = $userRows->map(function (object $user): array {
            $isCustomer = $user->user_type === 'customer';
            $lastLoginAt = $user->last_login_at ? Carbon::parse($user->last_login_at) : null;

            return [
                'id' => $user->id,
                'type' => $user->user_type,
                'type_label' => $isCustomer ? 'ผู้รับบริการ' : 'เจ้าหน้าที่',
                'name' => $isCustomer ? trim($user->first_name.' '.$user->last_name) : $user->admin_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'reference' => $isCustomer
                    ? ($user->reference_value ? "HN {$user->reference_value}" : 'ยังไม่มี HN')
                    : ($user->role_display ?? 'ยังไม่กำหนดบทบาท'),
                'activity' => $isCustomer
                    ? ((int) $user->activity_count).' การจอง'
                    : ($lastLoginAt ? 'เข้าใช้ล่าสุด '.$lastLoginAt->format('d/m/Y') : 'ยังไม่เคยเข้าใช้'),
                'is_active' => (bool) $user->is_active,
                'created_at' => $user->created_at ? Carbon::parse($user->created_at) : null,
            ];
        });
        $paginatedUsers = new LengthAwarePaginator(
            $users->values(),
            $totalUsers,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $paginatedUsers->withQueryString();

        $customerCount = $canViewCustomers ? Customer::count() : 0;
        $adminCount = $canViewAdmins ? Admin::count() : 0;
        $activeCustomerCount = $canViewCustomers ? Customer::where('is_active', true)->count() : 0;
        $activeAdminCount = $canViewAdmins ? Admin::where('is_active', true)->count() : 0;

        return view('admin.management.users', [
            'users' => $paginatedUsers,
            'stats' => [
                'total' => $customerCount + $adminCount,
                'active' => $activeCustomerCount + $activeAdminCount,
                'customers' => $customerCount,
                'admins' => $adminCount,
            ],
            'canViewCustomers' => $canViewCustomers,
            'canViewAdmins' => $canViewAdmins,
            'canManageCustomers' => $canManageCustomers,
            'canManageAdmins' => $canManageAdmins,
        ]);
    }

    public function customers(Request $request): View
    {
        $customers = Customer::withCount(['bookings', 'bookings as completed_bookings_count' => fn ($q) => $q->where('status', 'completed')])
            ->when($request->search, fn ($q, $term) => $q->where(fn ($s) => $s->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")))
            ->when($request->filled('state'), fn ($q) => $q->where('is_active', $request->boolean('state')))
            ->latest()->paginate(25)->withQueryString();

        return view('admin.management.customers', compact('customers'));
    }

    public function customer(Customer $customer): View
    {
        $bookingStats = $customer->bookings()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_count', [Booking::STATUS_COMPLETED])
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as active_count', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as cancelled_count', [Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW])
            ->first();
        $bookings = $customer->bookings()
            ->select(['id', 'booking_no', 'booking_date', 'time_slot_id', 'service_id', 'therapist_id', 'status'])
            ->with([
                'service:id,name_th,duration_minutes,price',
                'therapist:id,first_name,last_name,nickname',
                'timeSlot:id,start_time,end_time',
            ])
            ->latest('booking_date')
            ->latest('id')
            ->paginate(20, ['id', 'booking_no', 'booking_date', 'time_slot_id', 'service_id', 'therapist_id', 'status'], 'bookings_page')
            ->withQueryString();

        return view('admin.management.customer', compact('customer', 'bookingStats', 'bookings'));
    }

    public function saveCustomer(Request $request, ?Customer $customer = null): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^0[0-9]{8,9}$/', 'max:20'], 'email' => ['nullable', 'email', 'max:255'],
            'hn' => ['nullable', 'string', 'max:50'], 'gender' => ['required', 'in:male,female,other'],
            'birthdate' => ['nullable', 'date', 'before:today'], 'address' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'], 'is_active' => ['nullable', 'boolean'],
        ], ['phone.regex' => 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง']);
        $data['is_active'] = $request->boolean('is_active');
        $customer ? $customer->update($data) : $customer = Customer::create($data);
        AuditLog::record(auth('admin')->id(), $customer->wasRecentlyCreated ? 'customer_created' : 'customer_updated', $customer, null, $data);

        return back()->with('success', 'บันทึกข้อมูลผู้รับบริการแล้ว');
    }

    public function toggleCustomer(Customer $customer): RedirectResponse
    {
        $old = ['is_active' => $customer->is_active];
        $customer->update(['is_active' => ! $customer->is_active]);
        AuditLog::record(auth('admin')->id(), 'customer_status_changed', $customer, $old, ['is_active' => $customer->is_active]);

        return back()->with('success', $customer->is_active ? 'เปิดใช้งานผู้รับบริการแล้ว' : 'ระงับบัญชีผู้รับบริการแล้ว');
    }

    public function services(): View
    {
        return view('admin.management.services', ['services' => Service::with('therapists')->orderBy('sort_order')->paginate(25), 'therapists' => Therapist::where('is_active', true)->orderBy('first_name')->get()]);
    }

    public function saveService(Request $request, ?Service $service = null): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:services,code'.($service ? ','.$service->id : '')],
            'name_th' => ['required', 'string', 'max:255'], 'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'], 'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'], 'sort_order' => ['nullable', 'integer', 'min:0'],
            'requires_therapist_selection' => ['nullable', 'boolean'], 'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'], 'therapist_ids' => ['nullable', 'array'], 'therapist_ids.*' => ['integer', 'exists:therapists,id'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['requires_therapist_selection'] = $request->boolean('requires_therapist_selection');
        if ($request->hasFile('image')) {
            $oldImage = $service?->image;
            $data['image'] = $request->file('image')->store('services', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }
        $ids = $data['therapist_ids'] ?? [];
        unset($data['therapist_ids']);
        DB::transaction(function () use (&$service, $data, $ids) {
            $service ? $service->update($data) : $service = Service::create($data);
            $service->therapists()->sync($ids);
        });
        AuditLog::record(auth('admin')->id(), 'service_saved', $service, null, $data);

        return back()->with('success', 'บันทึกข้อมูลหัตถการแล้ว');
    }

    public function toggleService(Service $service): RedirectResponse
    {
        $service->update(['is_active' => ! $service->is_active]);
        AuditLog::record(auth('admin')->id(), 'service_status_changed', $service, null, ['is_active' => $service->is_active]);

        return back()->with('success', 'เปลี่ยนสถานะหัตถการแล้ว');
    }

    public function therapists(): View
    {
        return view('admin.management.therapists', [
            'therapists' => Therapist::query()
                ->select(['id', 'code', 'first_name', 'last_name', 'nickname', 'phone', 'gender', 'photo', 'bio', 'start_date', 'is_active'])
                ->with([
                    'services' => fn ($query) => $query->select(['services.id']),
                    'schedules' => fn ($query) => $query
                        ->select(['id', 'therapist_id', 'day_of_week', 'start_time', 'end_time', 'break_start', 'break_end', 'is_working'])
                        ->orderBy('day_of_week'),
                    'daysOff' => fn ($query) => $query
                        ->select(['id', 'therapist_id', 'date', 'reason'])
                        ->orderBy('date'),
                    'blockedPeriods' => fn ($query) => $query
                        ->select(['id', 'therapist_id', 'start_datetime', 'end_datetime', 'reason'])
                        ->orderBy('start_datetime'),
                ])
                ->orderBy('first_name')
                ->paginate(25),
            'services' => Service::query()
                ->select(['id', 'name_th'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function saveTherapist(Request $request, ?Therapist $therapist = null): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:therapists,code'.($therapist ? ','.$therapist->id : '')],
            'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'], 'phone' => ['nullable', 'regex:/^0[0-9]{8,9}$/'],
            'gender' => ['required', 'in:male,female,other'], 'bio' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'start_date' => ['nullable', 'date'], 'is_active' => ['nullable', 'boolean'], 'service_ids' => ['nullable', 'array'], 'service_ids.*' => ['integer', 'exists:services,id'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('photo')) {
            $oldPhoto = $therapist?->photo;
            $data['photo'] = $request->file('photo')->store('therapists', 'public');
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }
        $serviceIds = $data['service_ids'] ?? [];
        unset($data['service_ids']);
        DB::transaction(function () use (&$therapist, $data, $serviceIds) {
            $therapist ? $therapist->update($data) : $therapist = Therapist::create($data);
            $therapist->services()->sync($serviceIds);
        });
        AuditLog::record(auth('admin')->id(), 'therapist_saved', $therapist, null, $data);

        return back()->with('success', 'บันทึกข้อมูลหมอนวดแล้ว');
    }

    public function saveTherapistSchedule(Request $request, Therapist $therapist): RedirectResponse
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'], 'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'], 'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'date_format:H:i', 'after:break_start'], 'is_working' => ['nullable', 'boolean'],
        ]);
        $data['therapist_id'] = $therapist->id;
        $data['is_working'] = $request->boolean('is_working');
        TherapistSchedule::updateOrCreate(['therapist_id' => $therapist->id, 'day_of_week' => $data['day_of_week']], $data);
        AuditLog::record(auth('admin')->id(), 'therapist_schedule_saved', $therapist, null, $data);

        return back()->with('success', 'บันทึกตารางทำงานแล้ว');
    }

    public function addTherapistDayOff(Request $request, Therapist $therapist): RedirectResponse
    {
        $data = $request->validate(['date' => ['required', 'date'], 'reason' => ['nullable', 'string', 'max:255']]);
        TherapistDayOff::updateOrCreate(['therapist_id' => $therapist->id, 'date' => $data['date']], $data);

        return back()->with('success', 'เพิ่มวันหยุดหมอนวดแล้ว');
    }

    public function addTherapistBlock(Request $request, Therapist $therapist): RedirectResponse
    {
        $data = $request->validate(['start_datetime' => ['required', 'date'], 'end_datetime' => ['required', 'date', 'after:start_datetime'], 'reason' => ['nullable', 'string', 'max:255']]);
        $therapist->blockedPeriods()->create($data);

        return back()->with('success', 'เพิ่มช่วงปิดรับจองของหมอนวดแล้ว');
    }

    public function schedules(): View
    {
        return view('admin.management.schedules', [
            'schedules' => ClinicSchedule::orderBy('day_of_week')->get(),
            'holidays' => ClinicHoliday::orderByDesc('date')->paginate(20),
            'slots' => TimeSlot::orderBy('slot_number')->get(),
            'bookingEnabled' => (bool) Setting::get('booking_enabled', true),
        ]);
    }

    public function saveClinicSchedules(Request $request): RedirectResponse
    {
        $data = $request->validate(['schedules' => ['required', 'array'], 'schedules.*.open_time' => ['required', 'date_format:H:i'], 'schedules.*.close_time' => ['required', 'date_format:H:i', 'after:schedules.*.open_time'], 'schedules.*.max_bookings_per_slot' => ['required', 'integer', 'min:1', 'max:100']]);
        foreach ($data['schedules'] as $day => $row) {
            if ((int) $day < 0 || (int) $day > 6) {
                continue;
            }
            ClinicSchedule::updateOrCreate(['day_of_week' => (int) $day], [...$row, 'is_open' => $request->boolean("open.{$day}")]);
        }
        Setting::set('booking_enabled', $request->boolean('booking_enabled'), 'boolean', 'booking', 'เปิด/ปิดระบบรับจองออนไลน์', true);
        AuditLog::record(auth('admin')->id(), 'clinic_schedule_updated', null, null, $data);

        return back()->with('success', 'บันทึกวันเวลาเปิดคลินิกแล้ว');
    }

    public function saveSlots(Request $request): RedirectResponse
    {
        $data = $request->validate(['slots' => ['required', 'array', 'min:1', 'max:30'], 'slots.*.start_time' => ['required', 'date_format:H:i'], 'slots.*.end_time' => ['required', 'date_format:H:i', 'after:slots.*.start_time'], 'slots.*.label_th' => ['required', 'string', 'max:100']]);
        DB::transaction(function () use ($data) {
            TimeSlot::query()->update(['is_active' => false]);
            foreach (array_values($data['slots']) as $index => $slot) {
                $number = $index + 1;
                TimeSlot::updateOrCreate(['slot_number' => $number], [...$slot, 'is_active' => true]);
            }
        });

        return back()->with('success', 'บันทึกรอบเวลาแล้ว');
    }

    public function saveHoliday(Request $request, ?ClinicHoliday $holiday = null): RedirectResponse
    {
        $data = $request->validate(['date' => ['required', 'date'], 'name' => ['required', 'string', 'max:255'], 'is_full_day' => ['nullable', 'boolean'], 'start_time' => ['nullable', 'date_format:H:i'], 'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'], 'note' => ['nullable', 'string', 'max:2000']]);
        $data['is_full_day'] = $request->boolean('is_full_day');
        $holiday ? $holiday->update($data) : ClinicHoliday::updateOrCreate(['date' => $data['date']], $data);
        AuditLog::record(auth('admin')->id(), 'clinic_holiday_saved', $holiday, null, $data);

        return back()->with('success', 'บันทึกวันหยุดแล้ว');
    }

    public function deleteHoliday(ClinicHoliday $holiday): RedirectResponse
    {
        $holiday->delete();

        return back()->with('success', 'ลบวันหยุดแล้ว');
    }

    public function contents(): View
    {
        return view('admin.management.contents', ['contents' => Content::orderBy('sort_order')->orderByDesc('published_at')->paginate(25)]);
    }

    public function saveContent(Request $request, ?Content $content = null): RedirectResponse
    {
        $data = $request->validate(['type' => ['required', 'in:news,announcement,banner,faq'], 'title' => ['required', 'string', 'max:255'], 'content' => ['required', 'string', 'max:30000'], 'excerpt' => ['nullable', 'string', 'max:1000'], 'sort_order' => ['nullable', 'integer'], 'published_at' => ['nullable', 'date'], 'expired_at' => ['nullable', 'date', 'after_or_equal:published_at'], 'is_active' => ['nullable', 'boolean'], 'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096']]);
        if (! $content || empty($content->slug)) {
            $baseSlug = Str::slug($data['title']);
            $slugPrefix = $baseSlug !== '' ? $baseSlug : ($data['type'] ?? 'content');
            $data['slug'] = $slugPrefix.'-'.Str::lower(Str::random(6));
        }
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('contents', 'public');
        }
        $content ? $content->update($data) : $content = Content::create($data);
        AuditLog::record(auth('admin')->id(), 'content_saved', $content, null, $data);

        return back()->with('success', 'บันทึกเนื้อหาเว็บไซต์แล้ว');
    }

    public function toggleContent(Content $content): RedirectResponse
    {
        $content->update(['is_active' => ! $content->is_active]);

        return back()->with('success', 'เปลี่ยนสถานะเนื้อหาแล้ว');
    }

    public function admins(): View
    {
        return view('admin.management.admins', ['admins' => Admin::with('role')->paginate(25), 'roles' => Role::with('permissions')->get(), 'permissions' => Permission::orderBy('group')->orderBy('name')->get(), 'logs' => AuditLog::with('admin')->latest()->paginate(30, ['*'], 'logs_page')]);
    }

    public function saveAdmin(Request $request, ?Admin $admin = null): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:admins,email'.($admin ? ','.$admin->id : '')], 'role_id' => ['required', 'exists:roles,id'], 'password' => [$admin ? 'nullable' : 'required', 'string', 'min:12'], 'is_active' => ['nullable', 'boolean']]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['is_active'] = $request->boolean('is_active');
        $admin ? $admin->update($data) : $admin = Admin::create($data);
        AuditLog::record(auth('admin')->id(), 'admin_saved', $admin, null, ['name' => $admin->name, 'email' => $admin->email, 'role_id' => $admin->role_id, 'is_active' => $admin->is_active]);

        return back()->with('success', 'บันทึกบัญชีเจ้าหน้าที่แล้ว');
    }

    public function saveRolePermissions(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === 'super_admin', 422, 'ไม่สามารถแก้ไขสิทธิ์ Super Admin ได้');
        $data = $request->validate(['permission_ids' => ['nullable', 'array'], 'permission_ids.*' => ['integer', 'exists:permissions,id']]);
        $role->permissions()->sync($data['permission_ids'] ?? []);
        AuditLog::record(auth('admin')->id(), 'role_permissions_updated', $role, null, ['permission_ids' => $data['permission_ids'] ?? []]);

        return back()->with('success', 'บันทึกสิทธิ์ของบทบาทแล้ว');
    }

    public function toggleAdmin(Admin $admin): RedirectResponse
    {
        abort_if($admin->is(auth('admin')->user()), 422, 'ไม่สามารถปิดบัญชีของตนเองได้');
        $admin->update(['is_active' => ! $admin->is_active]);
        AuditLog::record(auth('admin')->id(), 'admin_status_changed', $admin, null, ['is_active' => $admin->is_active]);

        return back()->with('success', 'เปลี่ยนสถานะบัญชีเจ้าหน้าที่แล้ว');
    }
}
