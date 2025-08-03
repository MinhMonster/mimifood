<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TopUpTransactions extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
        'transaction_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     *
     * @param  Builder  $query
     * @param  Request  $request
     * @return Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $input = json_decode($request->input('input', '{}'));
        return $query->where(function ($q) use ($input) {
            if (isset($input->user_id)) {
                $q->orWhere('user_id', $input->user_id);
            }

            if (!empty($input->status)) {
                $q->orWhere('status', $input->status);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
