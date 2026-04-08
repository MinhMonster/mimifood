<?php

namespace App\Enums;

class AccountPurchaseStatus
{
    const CANCELLED_REFUNDED = 'cancelled_refunded';
    const CANCELLED_PENDING_REFUND = 'cancelled_pending_refund';
    const DEPOSIT = 'deposit';
    const INSTALLMENT_FIRST = 'installment_first';
    const COMPLETED = 'completed';
    const EXPIRED = 'expired';

    public static function all(): array
    {
        return [
            self::CANCELLED_REFUNDED,
            self::CANCELLED_PENDING_REFUND,
            self::DEPOSIT,
            self::INSTALLMENT_FIRST,
            self::COMPLETED,
            self::EXPIRED,
        ];
    }
}
