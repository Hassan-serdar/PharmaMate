<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Feedback;
use App\Enums\Role;
use App\Enums\FeedbackStatusEnum;
use App\Enums\FeedbackTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Notifications\Admin\NewFeedbackReceivedNotification;
use App\Notifications\FeedbackUpdatedNotification;

/**
 * هاد الملف هو اختبار شامل لكل شي بيتعلق بنظام الشكاوي والاقتراحات
 * بيغطي رحلة المستخدم ورحلة الأدمن بشكل كامل
 */
class FeedbackSystemTest extends TestCase
{
    use RefreshDatabase;

    // متغيرات مساعدة لنستخدمها بكل الاختبارات
    protected User $user;
    protected User $anotherUser;
    protected User $admin;

    // هي الميثود بتتنفذ قبل كل اختبار لتوفرلنا مستخدمين جاهزين
    protected function setUp(): void
    {
        parent::setUp();

        // منعمل مستخدم عادي
        $this->user = User::factory()->create(['role' => Role::USER]);
        
        // منعمل مستخدم تاني لنتأكد من صلاحيات الخصوصية
        $this->anotherUser = User::factory()->create(['role' => Role::USER]);

        // منعمل مستخدم أدمن
        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
    }

    // -----------------------------------------------------------------
    // اختبارات رحلة المستخدم (User Journey)
    // -----------------------------------------------------------------

    /** @test */
    public function a_guest_cannot_submit_feedback()
    {
        // الزائر (غير المسجل) ما لازم يقدر يبعت شكوى
        $this->postJson('/api/feedback', [])->assertUnauthorized();
    }

    /** @test */
    public function an_authenticated_user_can_submit_a_complaint_with_attachments()
    {
        // منستخدم أدوات وهمية لنمنع إرسال إيميلات أو رفع ملفات حقيقية أثناء الاختبار
        Notification::fake();
        Storage::fake('public');

        // منسجل دخول بالمستخدم العادي
        Sanctum::actingAs($this->user);

        $feedbackData = [
            'type' => FeedbackTypeEnum::COMPLAINT->value,
            'subject' => 'Order was late',
            'message' => 'My order #123 was very late.',
            'attachments' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->create('document.pdf'),
            ]
        ];

        $response = $this->postJson('/api/feedback', $feedbackData);

        // منتأكد إنه العملية نجحت ورجع الرد الصحيح
        $response->assertStatus(201)
                 ->assertJsonPath('data.subject', 'Order was late');

        // منتأكد إنه البيانات انحفظت صح بالداتا بيز
        $this->assertDatabaseHas('feedback', ['user_id' => $this->user->id, 'subject' => 'Order was late']);
        $this->assertDatabaseCount('attachments', 2);
        
        // منتأكد إنه الملفات ترفعت فعلاً
        $feedback = Feedback::first();
        Storage::disk('public')->assertExists($feedback->attachments->first()->path);

        // منتأكد إنه تم إرسال إشعار للأدمن
        Notification::assertSentTo($this->admin, NewFeedbackReceivedNotification::class);
    }
        /** @test */
        public function feedback_submission_fails_if_attachment_is_too_large()
    {
        Sanctum::actingAs($this->user);
        $feedbackData = [
            'type' => FeedbackTypeEnum::COMPLAINT->value,
            'subject' => 'Large file test',
            'message' => 'This should fail.',
            'attachments' => [UploadedFile::fake()->create('large_file.pdf', 6000)] // 6MB
        ];

        // نتأكد إنه رح يرجعلنا خطأ بالبيانات بسبب حجم الملف
        $this->postJson('/api/feedback', $feedbackData)
             ->assertStatus(422)
             ->assertJsonValidationErrors('attachments.0');
    }

    /** @test */
    public function a_user_can_view_only_their_own_feedback_tickets()
    {
        // منعمل شكوى للمستخدم تبعنا، وشكوى لمستخدم تاني
        Feedback::factory()->create(['user_id' => $this->user->id, 'subject' => 'My Ticket']);
        Feedback::factory()->create(['user_id' => $this->anotherUser->id, 'subject' => 'Not My Ticket']);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/my-feedback');
        
        // منتأكد إنه الرد بيحتوي بس على شكوى وحدة، وهي الشكوى تبعنا
        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.subject', 'My Ticket')
                 ->assertJsonMissing(['subject' => 'Not My Ticket']);
    }

    /** @test */
    public function a_user_can_update_their_feedback_only_if_status_is_new()
    {
        Sanctum::actingAs($this->user);

        $feedback = Feedback::factory()->create([
            'user_id' => $this->user->id,
            'status' => FeedbackStatusEnum::NEW,
        ]);
        
        $this->putJson("/api/feedback/{$feedback->id}", ['subject' => 'Updated Subject'])->assertOk();
        $this->assertDatabaseHas('feedback', ['id' => $feedback->id, 'subject' => 'Updated Subject']);
    }
    
    /** @test */
    public function a_user_cannot_update_their_feedback_if_status_is_not_new()
    {
        Sanctum::actingAs($this->user);

        $feedback = Feedback::factory()->create([
            'user_id' => $this->user->id,
            'status' => FeedbackStatusEnum::IN_PROGRESS, // الحالة تغيرت
        ]);
        
        // لازم يرجعله خطأ "ممنوع"
        $this->putJson("/api/feedback/{$feedback->id}", ['subject' => 'Updated Subject'])->assertForbidden();
    }
    /** @test */
    public function a_user_cannot_add_a_comment_to_a_resolved_ticket()
    {
        Sanctum::actingAs($this->user);
        $feedback = Feedback::factory()->create(['user_id' => $this->user->id, 'status' => FeedbackStatusEnum::RESOLVED]);
        $this->postJson("/api/feedback/{$feedback->id}/comments", ['comment' => 'Thank you!'])->assertForbidden();
    }

    /** @test */
    public function a_user_cannot_update_another_users_feedback()
    {
        Sanctum::actingAs($this->user);

        $feedback = Feedback::factory()->create([
            'user_id' => $this->anotherUser->id, // الشكوى تابعة لشخص آخر
        ]);
        
        $this->putJson("/api/feedback/{$feedback->id}", ['subject' => 'Updated Subject'])->assertForbidden();
    }
    /** @test */
    public function a_regular_user_cannot_access_admin_feedback_routes()
    {
        Sanctum::actingAs($this->user);
        
        // منجرب نفوت على رابط خاص بالأدمن
        $this->getJson('/api/admin/feedback')->assertForbidden();
    }
    /** @test */
    public function test_user_cannot_create_complaint_without_required_fields()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->postJson('/api/feedback', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['type', 'subject','message']);
    }
    /** @test */
    public function test_normal_user_cannot_update_complaint_status()
    {
        $user = User::factory()->create();
        $complaint = Feedback::factory()->create(['status' => FeedbackStatusEnum::NEW]);

        $response = $this->actingAs($user)
                        ->putJson("/api/feedback/{$complaint->id}", [
                            'status' => 'new'
                        ]);

        $response->assertStatus(403);
    }

    
    // -----------------------------------------------------------------
    //  (Admin Journey)
    // -----------------------------------------------------------------
    
    /** @test */
    public function an_admin_can_view_all_feedback_tickets()
    {
        // منعمل 5 شكاوي من مستخدمين مختلفين
        Feedback::factory()->count(5)->create();

        Sanctum::actingAs($this->admin);

        // منتأكد إنه الأدمن بيقدر يشوفهم كلهم
        $this->getJson('/api/admin/feedback')
             ->assertOk()
             ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function an_admin_can_update_a_ticket_status_and_the_user_is_notified()
    {
        Notification::fake();
        Sanctum::actingAs($this->admin);
        
        $feedback = Feedback::factory()->create(['user_id' => $this->user->id]);

        $this->putJson("/api/admin/feedback/{$feedback->id}", [
            'status' => FeedbackStatusEnum::IN_PROGRESS->value
        ])->assertOk();
        
        $this->assertDatabaseHas('feedback', [
            'id' => $feedback->id,
            'status' => FeedbackStatusEnum::IN_PROGRESS->value
        ]);

        // منتأكد إنه المستخدم صاحب الشكوى وصله إشعار بالتحديث
        Notification::assertSentTo($this->user, FeedbackUpdatedNotification::class);
    }
    
    /** @test */
    public function an_admin_can_add_a_public_comment_and_the_user_is_notified()
    {
        Notification::fake();
        Sanctum::actingAs($this->admin);

        $feedback = Feedback::factory()->create(['user_id' => $this->user->id]);

        $this->postJson("/api/admin/feedback/{$feedback->id}/comments", [
            'comment' => 'This is a public reply.',
            'is_private' => false,
        ])->assertOk();

        $this->assertDatabaseHas('feedback_comments', ['comment' => 'This is a public reply.', 'is_private' => false]);
        Notification::assertSentTo($this->user, FeedbackUpdatedNotification::class);
    }

    /** @test */
    public function an_admin_can_add_a_private_note_and_the_user_is_not_notified()
    {
        Notification::fake();
        Sanctum::actingAs($this->admin);

        $feedback = Feedback::factory()->create(['user_id' => $this->user->id]);

        $this->postJson("/api/admin/feedback/{$feedback->id}/comments", [
            'comment' => 'Internal note for the team.',
            'is_private' => true,
        ])->assertOk();

        $this->assertDatabaseHas('feedback_comments', ['is_private' => true]);
        // منتأكد إنه المستخدم ما وصله أي إشعار لأنه الملاحظة خاصة
        Notification::assertNotSentTo($this->user, FeedbackUpdatedNotification::class);
    }

}
