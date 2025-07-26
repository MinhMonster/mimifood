<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HidesTimestamps;

class Ninjas extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HidesTimestamps;

    protected $appends = ['active_discount'];
    protected $hidden = [
        'username',
        'password',
        'author_id',
    ];

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
        $search = $input->q;
        // return $search;
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            if (!empty($search->id)) {
                $q->orWhere('id', $search->id);
            }

            if (!empty($search->type)) {
                switch ($search->type) {
                    case "VIP":
                        $q->orWhere('type', 1);
                        break;
                    case "cheap":
                        $q->orWhere('type', 2);
                        break;
                    default:
                }
            }

            if (!empty($search->level)) {
                $q->orWhere('level', 'like', "%{$search->level}%");
            }

            if (!empty($search->class)) {
                $q->orWhere('class', 'like', "%{$search->class}%");
            }

            if (!empty($search->server)) {
                $q->orWhere('server', 'like', "%{$search->server}%");
            }

            if (!empty($search->username)) {
                $q->orWhere('username', 'like', "%{$search->username}%");
            }

            if (!empty($search->ingame)) {
                $q->orWhere('character_name', 'like', "%{$search->ingame}%");
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
