<?php

namespace App\Models;

use App\Traits\HidesTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Avatars extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;

    protected $appends = ['active_discount'];

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
        'sex'
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
        $input = json_decode($request->input('input', '{}'));
        $search = $input->q;

        if (empty((array) $search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            if (isset($search->id) && ctype_digit((string) $search->id)) {
                $q->orWhere('id', $search->id);
            }

            if (!empty($search->username)) {
                $q->orWhere('username', 'like', "%{$search->username}%");
            }
        });
    }

    public function discount()
    {
        return Discounts::where('type', 'avatar')
            ->where('is_active', true)
            ->first();
    }

    public function getActiveDiscountAttribute()
    {
        return calculatePercentFromTiers(
            $this->discount()->price_tiers ?? [],
            $this->selling_price
        );
    }
}
