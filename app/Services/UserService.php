<?php

namespace App\Services;

use App\Models\User;

/**
 * هاد الكلاس مسؤول عن إدارة بيانات المستخدمين
 */
class UserService
{
    /**
     * 
     *
     * @param User $user المستخدم الحالي
     * @param array $data البيانات الجديدة اللي تم التحقق منها
     * @return User المستخدم بعد التحديث
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    /**
     * حذف حساب المستخدم بشكل كامل
     *
     * @param User $user المستخدم الحالي
     * @return void
     */
    public function deleteAccount(User $user): void
    {
        // أول شي منسجل خروجه من كل الأجهزة
        $user->tokens()->delete();
        // بعدين منمسح حسابه
        $user->delete();
    }
}
