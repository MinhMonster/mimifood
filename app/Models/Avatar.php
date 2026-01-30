<?php

namespace App\Models;

use App\Traits\HidesTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;
use App\Traits\HasThumbnail;

class Avatar extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;
    use AccountRelations;
    use AccountAttributes;
    use HasThumbnail;

    protected $appends = ['active_discount', 'price', 'account_type', 'thumbnail'];

    protected $fillable = [
        'username',
        'description',
        'images',
        'is_full_image',
        'selling_price',
        'discount_percent',
        'land',
        'pets',
        'fish',
        'sex',
        'is_sold'
    ];

    protected $hidden = [
        'id',
        'is_sold',
        'transfer_pin',
        'purchase_price',
        'password'
    ];

    protected $casts = [
        'images' => 'array',
        'is_full_image' => 'boolean',
    ];

    /**
     * Scope search avatars
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
                !empty($filters['land']),
                fn($q) =>
                apply_range_filter($q, 'land', $filters['land'])
            )
            ->when(
                !empty($filters['cash']),
                fn($q) =>
                apply_range_filter($q, 'selling_price', $filters['cash'])
            )
            ->when(
                !empty($filters['sex']),
                fn($q) =>
                apply_range_filter($q, 'sex', $filters['sex'])
            )
            ->when(
                $filters['username'] ?? null,
                fn($q, $v) =>
                $q->where('username', 'like', "%{$v}%")
            );
    }

    public function getAccountTypeAttribute()
    {
        return 'avatar';
    }
}
