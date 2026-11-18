<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Service;
use App\Models\Therapist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_upload_a_therapist_photo(): void
    {
        Storage::fake('public');

        $admin = Admin::where('email', 'admin@wlu-clinic.local')->firstOrFail();
        $therapist = Therapist::firstOrFail();
        $service = Service::firstOrFail();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.therapists.update', $therapist), [
            'code' => $therapist->code,
            'first_name' => $therapist->first_name,
            'last_name' => $therapist->last_name,
            'nickname' => $therapist->nickname,
            'phone' => $therapist->phone,
            'gender' => $therapist->gender,
            'bio' => $therapist->bio,
            'start_date' => $therapist->start_date?->toDateString(),
            'is_active' => true,
            'service_ids' => [$service->id],
            'photo' => UploadedFile::fake()->createWithContent(
                'therapist.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
            ),
        ]);

        $response->assertRedirect();

        $therapist->refresh();

        $this->assertNotNull($therapist->photo);
        Storage::disk('public')->assertExists($therapist->photo);
    }

    public function test_admin_can_upload_a_service_image(): void
    {
        Storage::fake('public');

        $admin = Admin::where('email', 'admin@wlu-clinic.local')->firstOrFail();
        $service = Service::firstOrFail();
        $therapist = Therapist::firstOrFail();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.services.update', $service), [
            'code' => $service->code,
            'name_th' => $service->name_th,
            'name_en' => $service->name_en,
            'description' => $service->description,
            'price' => $service->price,
            'duration_minutes' => $service->duration_minutes,
            'sort_order' => $service->sort_order,
            'requires_therapist_selection' => true,
            'is_active' => true,
            'therapist_ids' => [$therapist->id],
            'image' => UploadedFile::fake()->createWithContent(
                'service.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
            ),
        ]);

        $response->assertRedirect();

        $service->refresh();

        $this->assertNotNull($service->image);
        Storage::disk('public')->assertExists($service->image);
    }
}
