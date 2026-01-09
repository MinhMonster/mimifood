<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Avatars extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['active_discount'];

    protected $fillable = [
        'code',
        'username',
        'password',
        'transfer_pin',
        'description',
        'images',
        'is_full_image',
        'selling_price',
        'purchase_price',
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
        $status = $input->status ?? 'active';

        if ($status === 'all') {
            $query->withTrashed();
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        if (empty((array) $input)) {
            return $query;
        }

        return $query->where(function ($q) use ($input) {
            if (isset($input->id) && ctype_digit((string) $input->id)) {
                $q->orWhere('id', $input->id);
            }

            if (!empty($input->code)) {
                $q->orWhere('code', $input->code);
            }

            if (!empty($input->sex)) {
                $q->orWhere('sex', $input->sex);
            }

            if (!empty($input->username)) {
                $q->orWhere('username', 'like', "%{$input->username}%");
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
        return calculatePercentFromTiers($this->discount()->price_tiers ?? [], $this->selling_price);
    }
}
