<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Ninjas extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['active_discount'];
    protected $fillable = [
        'code',
        'username',
        'password',
        'character_name',
        'transfer_pin',
        'description',
        'images',
        'is_full_image',
        'selling_price',
        'purchase_price',
        'discount_percent',
        'class',
        'level',
        'server',
        'weapon',
        'type',
        'tl_1',
        'tl_2',
        'tl_3',
        'tl_4',
        'tl_5',
        'tl_6',
        'tl_7',
        'tl_8',
        'tl_9',
        'tl_10',
        'tl_11',
        'tl_12',
        'item_1',
        'item_2',
        'item_3',
        'item_4',
        'item_5',
        'item_6',
        'item_7',
        'item_8',
        'item_9',
        'item_10',
        'item_11',
        'item_12',
        'item_13',
    ];

    /**
     *
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
    ];

    /**
     * Scope a query to search ninjas by id, username, or character_name.
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

            if (!empty($input->username)) {
                $q->orWhere('username', 'like', "%{$input->username}%");
            }

            if (!empty($input->character_name)) {
                $q->orWhere('character_name', 'like', "%{$input->character_name}%");
            }
        });
    }

    public function discount()
    {
        return Discounts::where('type', 'ninja')
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
