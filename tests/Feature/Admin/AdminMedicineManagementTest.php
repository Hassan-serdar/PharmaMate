<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Medicine;
use App\Enums\MedicineTypeEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMedicineManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pharmacist;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        $this->pharmacist = User::factory()->create(['role' => Role::PHARMACIST]);
    }

    /** @test */
    public function an_admin_can_create_a_new_medicine()
    {
        $medicineData = [
            'name' => 'Test Panadol',
            'active_ingredient' => 'Paracetamol',
            'dosage' => '500 mg',
            'type' => 'pills',
            'manufacturer' => 'Test Pharma',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/admin/medicines', $medicineData);
        
        $response->assertCreated();
        $this->assertDatabaseHas('medicines', ['name' => 'Test Panadol']);
    }
    
    /** @test */
    public function test_admin_can_create_a_medicine_with_an_image()
    {
        $file = UploadedFile::fake()->image('panadol.jpg');
        $medicineData = [
            'name' => 'Panadol',
            'active_ingredient'=>'dd',
            'dosage'=>'250 mg',
            'type' => MedicineTypeEnum::PILLS->value,
            'image' => $file,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/admin/medicines', $medicineData);

        $response->assertCreated();
        $medicine = Medicine::first();
        // التأكد من أن الصورة تم تخزينها
        Storage::disk('public')->assertExists($medicine->image_path);
        $this->assertDatabaseHas('medicines', ['name' => 'Panadol']);
    }

    /** @test */
    public function test_create_medicine_fails_with_missing_required_fields()
    {
        // نرسل طلب فارغ
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/admin/medicines', []);
        // نتوقع خطأ Validation
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'active_ingredient','dosage','type']); // التأكد من أسماء الحقول المطلوبة
    }


    /** @test */
    public function an_admin_can_update_a_medicine()
    {
        $medicine = Medicine::factory()->create();
        $updateData = ['_method'=>'PUT','name' => 'Updated Panadol'];

        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/admin/medicines/{$medicine->id}", $updateData);

        $response->assertOk();
        $this->assertDatabaseHas('medicines', ['id' => $medicine->id, 'name' => 'Updated Panadol']);
    }

    /** @test */
    public function test_update_medicine_fails_if_no_data_is_provided()
    {
        $medicine = Medicine::factory()->create();
        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/admin/medicines/{$medicine->id}", [
            '_method' => 'PUT'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('general');
    }

    /** @test */
    public function test_update_medicine_fails_with_duplicate_name()
    {
        $medicine1 = Medicine::factory()->create(['name' => 'Panadol']);
        $medicine2 = Medicine::factory()->create(['name' => 'Brufen']);

        $updateData = ['name' => 'Panadol']; // نحاول استخدام اسم موجود

        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/admin/medicines/{$medicine2->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    
     /** @test */
    public function an_admin_can_view_all_medicines()
    {
        Medicine::factory()->count(3)->create();
        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/admin/medicines');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_non_admin_cannot_view_all_medicines()
    {
        $response = $this->actingAs($this->pharmacist, 'sanctum')->getJson('/api/admin/medicines');
        $response->assertForbidden();
    }
    
     /** @test */
    public function an_admin_can_delete_a_medicine()
    {
        $medicine = Medicine::factory()->create();
        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/admin/medicines/{$medicine->id}");
        $response->assertOk();
        $this->assertDatabaseMissing('medicines', ['id' => $medicine->id]);
    }

    /** @test */
    public function test_deleting_a_medicine_also_deletes_its_image()
    {
        $file = UploadedFile::fake()->image('medicine.jpg');
        $medicine = Medicine::factory()->create(['image_path' => $file->store('medicine-images', 'public')]);

        // نتأكد أن الصورة موجودة قبل الحذف
        Storage::disk('public')->assertExists($medicine->image_path);

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/admin/medicines/{$medicine->id}");

        // نتأكد أن الصورة لم تعد موجودة بعد الحذف
        Storage::disk('public')->assertMissing($medicine->image_path);
    }

}