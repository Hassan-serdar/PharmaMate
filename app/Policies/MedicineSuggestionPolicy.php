<?php

namespace App\Policies;

use App\Models\MedicineSuggestion;
use App\Models\User;
use App\Enums\Role;

class MedicineSuggestionPolicy
{
    /**
     * الأدمن والسوبر أدمن بيقدروا يشوفوا كل الاقتراحات ويديروها
     */
    public function manage(User $user): bool
    {
        return $user->role === Role::ADMIN || $user->role === Role::SUPER_ADMIN;
    }

    /**
     * الصيدلاني بيقدر يقدم اقتراح
     */
    public function create(User $user): bool
    {
        return $user->role === Role::PHARMACIST;
    }
}
