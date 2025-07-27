<?php

namespace App\Models;

use App\Traits\HidesTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPurchaseHistory extends Model
{
    use SoftDeletes;
    use HidesTimestamps;

    protected $appends = ['purchased_at'];
    protected $hidden = [
        'user_id',
    ];
    protected $fillable = [
        'account_type',
        'account_id',
        'user_id',
        'price',
        'note',
    ];

    public function getPurchasedAtAttribute()
    {
        return $this->created_at->format('d-m-Y - H:i:s');
    }
}
