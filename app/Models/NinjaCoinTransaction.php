<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class NinjaCoinTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ninja_coin_transactions';

    protected $fillable = [
        'user_id',
        'character_name',
        'amount',
        'price',
        'coin',
        'server',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Scope search transactions
     *
     * @param  Builder  $query
     * @param  Request  $request
     * @return Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $input = json_decode($request->input('input', '{}'));

        return $query->where(function ($q) use ($input) {
            if (!empty($input->status)) {
                $q->where('status', $input->status);
            }

            if (!empty($input->user_id)) {
                $q->where('user_id', $input->user_id);
            }

            if (!empty($input->character_name)) {
                $q->where('character_name', 'like', '%' . $input->character_name . '%');
            }

            if (!empty($input->server)) {
                $q->where('server', $input->server);
            }
        });
    }

    /**
     * Relationship: user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
