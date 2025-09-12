<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPharmacyManagementTest extends TestCase
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
}