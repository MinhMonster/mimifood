<?php

namespace App\Traits\Account;

use App\Models\Discount;

trait AccountRelations
{
    public function discounts()
    {
        return Discount::where('type', $this->account_type)
            ->where('is_active', true)
            ->first();
    }
}
