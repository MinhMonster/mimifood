<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPurchaseHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_type',
        'account_id',
        'user_id',
        'price',
        'note',
    ];
}
