<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pharmacy;
use App\Enums\PharmacyStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPharmacyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pharmacist;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        $this->pharmacist = User::factory()->create(['role' => Role::PHARMACIST]);
        $this->user = User::factory()->create(['role' => Role::USER]);


    }


    /** @test */
    public function an_admin_can_create_a_pharmacy_for_a_user()
    {
        $pharmacyData = Pharmacy::factory()->make([
            'user_id' => $this->pharmacist->id,
        ])->toArray();

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/admin/pharmacies', $pharmacyData);

        $response->assertCreated()->assertJsonPath('data.name', $pharmacyData['name']);
        $this->assertDatabaseHas('pharmacies', ['name' => $pharmacyData['name'], 'user_id' => $this->pharmacist->id]);
    }
    /** @test */
    public function an_admin_can_update_a_pharmacy()
    {
        $pharmacy = Pharmacy::factory()->create(['user_id' => $this->pharmacist->id]);
        $updateData = ['name' => 'Updated Pharmacy Name'];

        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/admin/pharmacies/{$pharmacy->id}", $updateData);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Pharmacy Name');
        $this->assertDatabaseHas('pharmacies', ['id' => $pharmacy->id, 'name' => 'Updated Pharmacy Name']);
    }

        /** @test */
    public function an_admin_can_filter_pharmacies_by_status()
    {
        // إنشاء صيدليات بحالات مختلفة
        Pharmacy::factory()->create(['status' => PharmacyStatusEnum::ONLINE]);
        Pharmacy::factory()->count(2)->create(['status' => PharmacyStatusEnum::OFFLINE]);

        // طلب الصيدليات المعلقة فقط
        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/pharmacies?status=offline');

        $response->assertOk()
            // يجب أن تكون النتيجة صيدليتين فقط
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function non_admin_users_are_forbidden_from_managing_pharmacies()
    {
        $pharmacy = Pharmacy::factory()->create();

        // اختبار الصيدلاني
        $this->actingAs($this->pharmacist, 'sanctum')->getJson('/api/admin/pharmacies')->assertForbidden();
        $this->actingAs($this->pharmacist, 'sanctum')->putJson("/api/admin/pharmacies/{$pharmacy->id}", [])->assertForbidden();

        // اختبار المستخدم العادي
        $this->actingAs($this->user, 'sanctum')->getJson('/api/admin/pharmacies')->assertForbidden();
        $this->actingAs($this->user, 'sanctum')->putJson("/api/admin/pharmacies/{$pharmacy->id}", [])->assertForbidden();
    }

    /** @test */
    public function admin_cannot_create_a_pharmacy_with_a_duplicate_name_for_the_same_owner()
    {
        // إنشاء صيدلية أولى لمالك معين
        $owner = User::factory()->create(['role' => Role::PHARMACIST]);
        Pharmacy::factory()->create([
            'name' => 'City Pharmacy',
            'user_id' => $owner->id
        ]);

        // محاولة إنشاء صيدلية ثانية بنفس الاسم ونفس المالك
        $duplicateData = Pharmacy::factory()->make([
            'name' => 'City Pharmacy', // نفس الاسم
            'user_id' => $owner->id,     // نفس المالك
        ])->toArray();

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/admin/pharmacies', $duplicateData);

        // نتوقع خطأ validation
        $response->assertStatus(422)
            ->assertJsonValidationErrors('user_id');
    }

}