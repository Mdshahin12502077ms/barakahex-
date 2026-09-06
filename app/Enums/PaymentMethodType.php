<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case BANK   = 'bank';
    case MFS    = 'mfs';
    case CASH   = 'cash';


}
