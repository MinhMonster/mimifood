<?php

namespace App\Models;

use App\Models\Ninjas;
use App\Models\Avatars;
use App\Traits\HidesTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPurchaseHistory extends Model
{
    use SoftDeletes;
    use HidesTimestamps;

    protected $appends = ['purchased_at', 'account'];
    protected $hidden = [
        'user_id',
        'images',
        'note',
        'purchase_price'
    ];
    protected $fillable = [
        'account_type',
        'account_code',
        'user_id',
        'selling_price',
        'purchase_price',
        'images',
        'note',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function getPurchasedAtAttribute()
    {
        return $this->created_at->format('d-m-Y - H:i:s');
    }

    public function getAccountAttribute()
    {
        if (!$this->account_type || !$this->account_code) {
            return null;
        }

        switch ($this->account_type) {
            case 'ninja':
                $account = Ninjas::query()
                    ->select('code', 'character_name', 'username', 'password', 'transfer_pin')
                    ->where('code', $this->account_code)->first();

                if (!$account) {
                    return null;
                }

                return [
                    'code' => $account->code,
                    'character_name' => $account->character_name,
                    'username' => $account->username,
                    'password' => $account->password,
                    'transfer_pin' => $account->transfer_pin,
                ];

            case 'avatar':
                $account = Avatars::query()
                    ->select('code', 'username', 'password', 'transfer_pin')
                    ->where('code', $this->account_code)->first();

                if (!$account) {
                    return null;
                }

                return [
                    'code' => $account->code,
                    'username' => $account->username,
                    'password' => $account->password,
                    'transfer_pin' => $account->transfer_pin,
                ];

            default:
                return null;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
