<?php

namespace App\Enums;

class AccountPurchaseType
{
    const NORMAL = 'normal';
    const DEPOSIT = 'deposit';
    const INSTALLMENTS = 'installments';

    public static function all(): array
    {
        return [
            self::NORMAL,
            self::DEPOSIT,
            self::INSTALLMENTS,
        ];
    }
}
