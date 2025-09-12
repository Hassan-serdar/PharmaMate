<?php

namespace App\Enums;

enum MedicineTypeEnum: string
{
    case PILLS = 'pills'; // حبوب
    case SUPPOSITORY = 'suppository'; // تحاميل
    case SYRUP = 'syrup'; // شراب
    case INJECTION = 'injection'; // إبرة
}