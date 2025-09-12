<?php

namespace App\Policies;

use App\Models\Pharmacy;
use App\Models\User;
use App\Enums\Role;

class PharmacyPolicy
{
    /**
     * تحديد إذا كان المستخدم بيقدر يدير مخزون هي الصيدلية
     */
    public function manageInventory(User $user, Pharmacy $pharmacy): bool
    {
        // الشرط: لازم يكون دوره صيدلاني و هو صاحب هي الصيدلية
        return $user->role === Role::PHARMACIST && $user->id === $pharmacy->user_id;
    }

    public function store(User $user): bool
    {
        // الشرط: لازم يكون دوره صيدلاني و هو صاحب هي الصيدلية
        return $user->role === Role::ADMIN ||$user->role === Role::SUPER_ADMIN;
    }

    public function update(User $user, Pharmacy $pharmacy)
    {
        return $user->role === Role::ADMIN ||$user->role === Role::SUPER_ADMIN;
    }

        public function Pharmacistupdate(User $user)
    {
        $pharmacy = auth()->user()->pharmacy;

        // الشرط: لازم يكون دوره صيدلاني و هو صاحب هي الصيدلية
        return $user->role === Role::PHARMACIST && $user->id === $pharmacy->user_id;
    }


    public function delete(User $user, Pharmacy $pharmacy)
    {
        return $user->role === Role::ADMIN || $user->role === Role::SUPER_ADMIN;
    }
}
