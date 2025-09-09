<?php

namespace App\Enums;

/**
 * هاد الـ Enum بيحدد الحالات الممكنة للصيدلية.
 * القيمة النصية (string) هي اللي بتتخزن بالداتا بيز.
 */
enum PharmacyStatusEnum: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
}
