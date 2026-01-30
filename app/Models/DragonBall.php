<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use App\Traits\HidesTimestamps;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;
use App\Traits\HasThumbnail;

class DragonBall extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;
    use AccountRelations;
    use AccountAttributes;
    use HasThumbnail;

    protected $table = 'dragon_balls';

    protected $appends = ['active_discount', 'price', 'account_type', 'thumbnail'];

    protected $hidden = [
        'id',
        'username',
        'is_sold',
        'purchase_price',
        'password'
    ];

    protected $casts = [
        'images' => 'array',
        'is_sold' => 'boolean',
        'is_family' => 'boolean',
        'is_full_image' => 'boolean',
        'selling_price' => 'decimal:0',
        'purchase_price' => 'decimal:0',
        'discount_percent' => 'decimal:0',
    ];

    /**
     * Default attributes.
     */
    protected $attributes = [
        'is_sold' => false,
    ];

    /* =====================
     |  Scopes (optional)
     ===================== */

    public function scopeAvailable($query)
    {
        return $query->where('is_sold', false);
    }

    public function scopeSold($query)
    {
        return $query->where('is_sold', true);
    }

    /**
     * Scope a query to search
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $filters = $request->all();

        return $query
            ->when(
                $filters['code'] ?? null,
                fn($q, $v) =>
                $q->where('code', 'like', "%{$v}%")
            )
            ->when(
                $filters['planet'] ?? null,
                fn($q, $v) =>
                $q->where('planet', $v)
            )
            ->when(
                !empty($filters['cash']),
                fn($q) =>
                apply_range_filter($q, 'selling_price', $filters['cash'])
            )
            ->when(
                $filters['username'] ?? null,
                fn($q, $v) =>
                $q->where('username', 'like', "%{$v}%")
            );
    }
    public function getAccountTypeAttribute()
    {
        return 'dragon_ball';
    }
}
