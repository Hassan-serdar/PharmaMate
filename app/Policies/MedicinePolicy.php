<?php

namespace App\Policies;

use App\Models\Medicine;
use App\Models\User;
use App\Enums\Role;

class MedicinePolicy
{
    /**
     * الأدمن والسوبر أدمن فقط بيقدروا يديروا جدول الأدوية المركزي
     */
    public function manage(User $user): bool
    {
        return $user->role === Role::ADMIN || $user->role === Role::SUPER_ADMIN;
    }

    public function viewAny(User $user): bool
    {
        // اسمح للأدمن والصيدلاني برؤية قائمة الأدوية
        return $user->role === Role::ADMIN || $user->role === Role::SUPER_ADMIN || $user->role === Role::PHARMACIST;
    }

}
