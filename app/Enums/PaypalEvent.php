<?php

namespace App\Enums;

enum PaypalEvent: string
{
    case COMPLETED = 'PAYMENT.SALE.COMPLETED';
    case DENIED = 'PAYMENT.SALE.DENIED';
    case CANCELLED = 'PAYMENT.SALE.CANCELLED';
    case REVERSED = 'PAYMENT.SALE.REVERSED';
}
