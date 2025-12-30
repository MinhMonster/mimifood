<?php

namespace App\Traits\Account;

use App\Models\Discounts;

trait AccountRelations
{
    public function discount()
    {
        return Discounts::where('type', $this->account_type)
            ->where('is_active', true)
            ->first();
    }
}
