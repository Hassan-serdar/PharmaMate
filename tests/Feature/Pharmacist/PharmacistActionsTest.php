<?php

namespace Tests\Feature\Pharmacist;

use App\Enums\Role;
use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacistActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $pharmacist;
    private Pharmacy $pharmacy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pharmacist = User::factory()->create(['role' => Role::PHARMACIST]);
        $this->pharmacy = Pharmacy::factory()->create(['user_id' => $this->pharmacist->id]);
    }

    /** @test */
    public function a_pharmacist_can_update_their_own_pharmacy()
    {
        $updateData = ['phone_number' => '555-9876', 'status' => 'offline'];
        $response = $this->actingAs($this->pharmacist, 'sanctum')->putJson('/api/pharmacist/pharmacy', $updateData);
        $response->assertOk()->assertJsonPath('data.phone_number', '555-9876');
    }

    /** @test */
    public function a_pharmacist_can_add_a_medicine_to_inventory()
    {
        $medicine = Medicine::factory()->create();
        $inventoryData = ['medicine_id'=>$medicine->id,'price' => 10, 'quantity' => 100];
        $response = $this->actingAs($this->pharmacist, 'sanctum')->postJson("/api/pharmacist/inventory", $inventoryData);
        $response->assertCreated();
        $this->assertDatabaseHas('medicine_pharmacy', ['pharmacy_id' => $this->pharmacy->id, 'medicine_id' => $medicine->id]);
    }

    /** @test */
    public function a_pharmacist_can_create_a_medicine_suggestion()
    {
        $suggestionData = ['name' => 'New Med', 'active_ingredient' => 'Suggestinum', 'dosage'=> '500 mg','type' => 'suppository',];
        $response = $this->actingAs($this->pharmacist, 'sanctum')->postJson('/api/pharmacist/suggestions', $suggestionData);
        $response->assertCreated()->assertJsonPath('data.suggested_medicine.name', 'New Med');
    }
}