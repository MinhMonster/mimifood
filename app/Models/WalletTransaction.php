<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
    ];

    /**
     * Quan hệ: Mỗi giao dịch thuộc về một user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
