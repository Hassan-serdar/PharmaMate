<?php

namespace App\Enums;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case PHARMACIST = 'pharmacist';
    case DELIVERY = 'delivery';
    case WAREHOUSE_OWNER = 'warehouse_owner';
}
