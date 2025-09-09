<?php

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'super_admin';
    case USER = 'user';
    case ADMIN = 'admin';
    case PHARMACIST = 'pharmacist';
    case DELIVERY = 'delivery';
    case WAREHOUSE_OWNER = 'warehouse_owner';
}
