<?php

namespace App\Traits\Account;

use App\Models\Discounts;

trait AccountRelations
{
    public function discounts()
    {
        return Discounts::where('type', $this->account_type)
            ->where('is_active', true)
            ->first();
    }
}
