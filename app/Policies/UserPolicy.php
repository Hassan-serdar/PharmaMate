<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Role;

class UserPolicy
{
    /**
     * تحديد إذا كان المستخدم بيقدر يحذف حساب (سواء حسابه أو حساب حدا تاني)
     *
     * @param User $currentUser المستخدم اللي عم يعمل الطلب (المُنفّذ)
     * @param User $targetUser المستخدم اللي رح ينحذف (الهدف)
     * @return bool
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
         // القاعدة رقم 1 (الأهم): السوبر أدمن لا يمكن حذفه أبداً
        if ($targetUser->role === Role::SUPER_ADMIN) {
            return false;
        }

        // القاعدة رقم 2: السوبر أدمن بيقدر يحذف أي حدا تاني (طالما هو مو سوبر أدمن)
        if ($currentUser->role === Role::SUPER_ADMIN) {
            return true;
        }
        
        // القاعدة رقم 3: المستخدم بيحذف حاله (مع استثناء الأدمن)
        if ($currentUser->id === $targetUser->id) {
            // إذا كان المستخدم اللي عم يحاول يحذف حاله هو أدمن، منمنعه
            if ($currentUser->role === Role::ADMIN) {
                return false;
            }
            // إذا كان أي حدا تاني (user, pharmacist...) عم يحذف حاله، منسمحله
            return true;
        }

        // القاعدة رقم 4: الأدمن العادي بيقدر يحذف المستخدمين التانيين (مع شروط)
        if ($currentUser->role === Role::ADMIN) {
            // بس ما بيقدر يحذف أدمن متله 
            if ($targetUser->role === Role::ADMIN) {
                return false;
            }
            // إذا مرق من كل الشروط، بيقدر يحذف (user, pharmacist,...)
            return true;
        }

        // القاعدة الأخيرة: إذا ما انطبقت أي قاعدة من اللي فوق، فالعملية ممنوعة
        // (متل مستخدم عادي عم يحاول يحذف مستخدم تاني)
        return false;

    }
}

