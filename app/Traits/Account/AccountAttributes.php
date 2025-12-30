<?php

namespace App\Traits\Account;

trait AccountAttributes
{

    public function getActiveDiscountAttribute()
    {
        return calculatePercentFromTiers(
            $this->discount()->price_tiers ?? [],
            $this->selling_price
        );
    }

    public function getPriceAttribute()
    {
        $sellingPrice = (int) $this->selling_price;

        $discountPercent = (int) ($this->active_discount ?? 0);

        if ($discountPercent <= 0) {
            return $sellingPrice;
        }

        $finalPrice = $sellingPrice * (100 - $discountPercent) / 100;

        return max(0, (int) round($finalPrice));
    }
}
