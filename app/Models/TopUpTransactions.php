<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TopUpTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'account_holder_name',
        'bank_name',
        'amount',
        'transaction_at',
        'status',
    ];
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
            if (!empty($input->status)) {
                $q->orWhere('status', $input->status);
            }
        });
    }
}
