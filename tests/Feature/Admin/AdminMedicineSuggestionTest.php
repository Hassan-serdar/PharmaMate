<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Enums\SuggestionStatusEnum;
use App\Models\MedicineSuggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMedicineSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private MedicineSuggestion $suggestion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        $this->suggestion = MedicineSuggestion::factory()->create(['status' => SuggestionStatusEnum::PENDING]);
    }

    /** @test */
    public function an_admin_can_approve_a_medicine_suggestion()
    {
        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/admin/suggestions/{$this->suggestion->id}", [
            'approved' => true,
        ]);

        // تحقق من أن الحالة تغيرت إلى approved
        $response->assertOk()->assertJsonPath('data.status', 'approved');

        // تأكد من أن الدواء تم إنشاؤه بالاسم الصحيح
        $this->assertDatabaseHas('medicines', [
            'name' => $this->suggestion->name, 
        ]);
    }

    /** @test */
    public function an_admin_can_reject_a_medicine_suggestion()
    {
        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/admin/suggestions/{$this->suggestion->id}", [
            'approved' => false,
            'rejection_reason' => 'Duplicate item.',
        ]);

        $response->assertOk()->assertJsonPath('data.status', 'rejected');
        $this->assertDatabaseMissing('medicines', ['name' => $this->suggestion->name]);
    }
}