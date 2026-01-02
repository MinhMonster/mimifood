<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HidesTimestamps;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;

class Ninjas extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;
    use AccountRelations;
    use AccountAttributes;

    protected $appends = ['active_discount', 'price', 'account_type'];
    protected $hidden = [
        'id',
        'username',
        'is_sold',
        'transfer_pin'
    ];

    protected $casts = [
        'images' => 'array',
        'is_family' => 'boolean',
        'is_full_image' => 'boolean',
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
        $search = $input->q ?? null;
        // return $search;
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            if (!empty($search->code)) {
                $q->where('code', $search->code);
            }

            if (!empty($search->type)) {
                switch ($search->type) {
                    case "VIP":
                        $q->where('type', 1);
                        break;
                    case "cheap":
                        $q->where('type', 2);
                        break;
                    default:
                }
            }

            if (!empty($search->level)) {
                if (isset($search->level->min)) {
                    $q->where('level', '>=', $search->level->min);
                }

                if (isset($search->level->max)) {
                    $q->where('level', '<=', $search->level->max);
                }
            }

            if (!empty($search->cash)) {
                if (isset($search->cash->min)) {
                    $q->where('selling_price', '>=', $search->cash->min);
                }

                if (isset($search->cash->max)) {
                    $q->where('selling_price', '<=', $search->cash->max);
                }
            }

            if (!empty($search->class)) {
                $q->where('class', 'like', "%{$search->class}%");
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
        return 'ninja';
    }
}
