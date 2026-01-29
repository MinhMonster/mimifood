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
    ];

    /**
     * Scope a query to search avatars by id, username.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $search = $request;

        if (empty((array) $search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            if (!empty($search->code)) {
                $q->where('code', $search->code);
            }

            if (!empty($search->username)) {
                $q->where('username', 'like', "%{$search->username}%");
            }
        });
    }

    public function getAccountTypeAttribute()
    {
        return 'avatar';
    }
}
