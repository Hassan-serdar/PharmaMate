<?php

// tests/Feature/Auth/AuthenticationTest.php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------
    // اختبارات عملية التسجيل (Registration)
    // -----------------------------------------------------------------

    /** @test */
    public function a_visitor_can_register_as_a_normal_user_and_gets_a_token()
    {
        $userData = [
            'firstname' => 'حسان',
            'lastname' => 'سردار',
            'email' => 'test@example.com',
            'phonenumber' => '0912345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['user', 'access_token']]);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => Role::USER->value
        ]);
    }

    /** @test */
    public function registration_fails_with_validation_errors_for_missing_data()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['firstname', 'email', 'password']);
    }

    /** @test */
    public function registration_fails_if_email_is_already_taken()
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/register', [
            'firstname' => 'حسان',
            'lastname' => 'سردار',
            'email' => 'taken@example.com',
            'phonenumber' => '0912345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function an_admin_can_register_a_new_user_with_a_specific_role()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN->value]);
        
        Sanctum::actingAs($admin);

        $userData = [
            'firstname' => 'صيدلاني',
            'lastname' => 'جديد',
            'email' => 'pharmacist@example.com',
            'phonenumber' => '0987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => Role::PHARMACIST->value,
        ];
        
        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'pharmacist@example.com',
            'role' => Role::PHARMACIST->value, 
        ]);
    }

    // -----------------------------------------------------------------
    // اختبارات عملية تسجيل الدخول (Login)
    // -----------------------------------------------------------------

    /** @test */
    public function a_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['user', 'access_token']]);
    }

    /** @test */
    public function login_fails_with_incorrect_password()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password', // كلمة سر غلط
        ]);
        
        $response->assertStatus(401);
    }

    // -----------------------------------------------------------------
    // اختبارات الملف الشخصي (Profile)
    // -----------------------------------------------------------------

    /** @test */
    public function an_authenticated_user_can_view_their_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user); 
        $response = $this->getJson('/api/profile');

        $response->assertStatus(200);
        $response->assertJsonFragment(['email' => $user->email]); 
    }
    
    /** @test */
    public function an_unauthenticated_user_cannot_view_a_profile()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function a_user_can_update_their_profile_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updateData = ['firstname' => 'اسم معدل'];

        $response = $this->putJson('/api/profile', $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'firstname' => 'اسم معدل']);
    }

    /** @test */
    public function a_user_can_update_their_password_with_correct_current_password()
    {
        $plainPassword = 'old-password';
        $user = User::factory()->create(['password' => $plainPassword]);
        Sanctum::actingAs($user);
        $updateData = [
            'current_password' => $plainPassword,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ];
        $response = $this->putJson('/api/profile', $updateData);
        $response->assertStatus(200);
    }

    /** @test */
    public function updating_profile_fails_if_no_data_is_provided()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', []); 

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('general'); 
    }

    /** @test */
    public function an_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $user->tokens);
    }
    
    /** @test */
    public function an_authenticated_user_can_delete_their_account()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/profile');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}