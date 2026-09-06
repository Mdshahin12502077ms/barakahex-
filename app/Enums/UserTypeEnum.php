<?php

namespace App\Enums;

enum UserTypeEnum: string
{
    case DELIVERY       = 'delivery';
    case MERCHANT       = 'merchant';
    case MERCHANT_STAFF = 'merchant_staff';
    case STAFF          = 'staff';

}
