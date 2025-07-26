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
        'username',
        'password',
        'character_name',
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
        'tl1',
        'tl2',
        'tl3',
        'tl4',
        'tl5',
        'tl6',
        'tl7',
        'tl8',
        'tl9',
        'tl10',
        'yoroi',
        'eye',
        'book',
        'cake',
        'yen',
        'clone',
        'disguise',
        'mounts',
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
