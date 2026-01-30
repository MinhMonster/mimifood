<?php

namespace App\Models\Base;


use App\Models\Discount;
use App\Traits\HidesTimestamps;
use App\Traits\HasThumbnail;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseGameAccountModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;
    use HasThumbnail;
    use Filterable;


    protected $appends = [
        'active_discount',
        'price',
        'account_type',
        'thumbnail',
    ];

    protected $attributes = [
        'is_sold' => false,
    ];

    /* ================== SCOPES ================== */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_sold', false);
    }

    public function scopeSold(Builder $query): Builder
    {
        return $query->where('is_sold', true);
    }

    public function discounts()
    {
        return Discount::where('type', $this->account_type)
            ->where('is_active', true)
            ->first();
    }

    /* ================== ATTRIBUTES ================== */

    public function getAccountTypeAttribute(): string
    {
        return Str::snake(class_basename($this));
    }

    public function getActiveDiscountAttribute(): int
    {
        $tiers = optional($this->discounts())->price_tiers ?? [];

        return calculatePercentFromTiers(
            $tiers,
            (int) $this->selling_price
        );
    }

    public function getPriceAttribute(): int
    {
        $sellingPrice = (int) $this->selling_price;
        $discount = (int) ($this->active_discount ?? 0);

        if ($discount <= 0) {
            return $sellingPrice;
        }

        return max(0, (int) round(
            $sellingPrice * (100 - $discount) / 100
        ));
    }

    public function getProfitAttribute(): int
    {
        return (int) $this->price - (int) ($this->purchase_price ?? 0);
    }
}
