<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;

class DragonBall extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AccountRelations;
    use AccountAttributes;

    protected $table = 'dragon_balls';

    protected $appends = ['active_discount', 'price', 'profit', 'account_type'];

    protected $fillable = [
        'code',
        'username',
        'password',
        'strength',
        'disciple',
        'images',
        'selling_price',
        'purchase_price',
        'discount_percent',
        'planet',
        'server',
        'type',
        'description',
        'is_sold',
    ];

    /**
     * The attributes that should be cast.
     */
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

    /**
     * Scope a query to search
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $input = json_decode($request->input('input', '{}'));
        $search = $input->q ?? null;
        // return $search;
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            if (!empty($search->code)) {
                $q->where('code', $search->code);
            }


            if (!empty($search->cash)) {
                if (isset($search->cash->min)) {
                    $q->where('selling_price', '>=', $search->cash->min);
                }

                if (isset($search->cash->max)) {
                    $q->where('selling_price', '<=', $search->cash->max);
                }
            }

            if (!empty($search->planet)) {
                $q->where('planet', 'like', "%{$search->planet}%");
            }

            if (!empty($search->server)) {
                $q->where('server', 'like', "%{$search->server}%");
            }

            if (!empty($search->username)) {
                $q->where('username', 'like', "%{$search->username}%");
            }

            if (!empty($search->ingame)) {
                $q->where('character_name', 'like', "%{$search->ingame}%");
            }

            if (!empty($search->family)) {
                $q->where('is_family', $search->family);
            }
        });
    }

    public function getAccountTypeAttribute()
    {
        return 'dragon_ball';
    }
}
