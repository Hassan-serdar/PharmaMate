<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;
use App\Enums\Role;
use App\Enums\FeedbackStatusEnum;

class FeedbackPolicy
{
    /**
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === Role::ADMIN || $user->role === Role::SUPER_ADMIN) {
            return true;
        }
        return null; // إذا مو أدمن منخلي باقي القواعد تقرر
    }
    
    /**
     * تحديد إذا كان المستخدم بيقدر يشوف قائمة برسائله
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * تحديد إذا كان المستخدم بيقدر يشوف تفاصيل رسالة معينة
     */
    public function view(User $user, Feedback $feedback): bool
    {
        // لازم يكون هو صاحب الرسالة
        return $user->id === $feedback->user_id;
    }

    /**
     * تحديد إذا كان المستخدم بيقدر يعدل رسالته
     */
    public function update(User $user, Feedback $feedback): bool
    {
        // لازم يكون هو صاحب الرسالة و حالة الرسالة لسا "جديدة"
        return $user->id === $feedback->user_id && $feedback->status === FeedbackStatusEnum::NEW;
    }

    /**
     * تحديد إذا كان المستخدم بيقدر يحذف رسالته
     */
    public function delete(User $user, Feedback $feedback): bool
    {
        // نفس قاعدة التعديل
        return $user->id === $feedback->user_id && $feedback->status === FeedbackStatusEnum::NEW;
    }
}