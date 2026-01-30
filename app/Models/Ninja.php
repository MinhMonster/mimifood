<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HidesTimestamps;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;
use App\Traits\HasThumbnail;

class Ninja extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;
    use AccountRelations;
    use AccountAttributes;
    use HasThumbnail;

    protected $appends = ['active_discount', 'price', 'account_type', 'thumbnail'];
    protected $hidden = [
        'id',
        'username',
        'password',
        'is_sold',
        'transfer_pin',
        'purchase_price'
    ];

    protected $casts = [
        'images' => 'array',
        'is_family' => 'boolean',
        'is_full_image' => 'boolean',
    ];

    /**
     * Scope search ninja accounts
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
                $filters['type'] ?? null,
                fn($q, $v) =>
                $q->where('type', $v)
            )

            ->when(
                array_key_exists('is_family', $filters),
                fn($q) =>
                $q->where('is_family', $filters['is_family'])
            )

            ->when(
                !empty($filters['level']),
                fn($q) =>
                apply_range_filter($q, 'level', $filters['level'])
            )

            ->when(
                !empty($filters['cash']),
                fn($q) =>
                apply_range_filter($q, 'selling_price', $filters['cash'])
            )

            ->when(
                $filters['class'] ?? null,
                fn($q, $v) =>
                $q->where('class', $v)
            )
            ->when(
                $filters['server'] ?? null,
                fn($q, $v) =>
                $q->where('server', $v)
            )
            ->when(
                $filters['character_name'] ?? null,
                fn($q, $v) =>
                $q->where('character_name', 'like', "%{$v}%")
            );
    }

    public function getAccountTypeAttribute()
    {
        return 'ninja';
    }
}
