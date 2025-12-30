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

    public function getAccountAttribute()
    {
        if (!$this->account_type || !$this->account_id) {
            return null;
        }

        switch ($this->account_type) {
            case 'ninja':
                $account = Ninjas::query()
                    ->select('id', 'character_name', 'username', 'password', 'transfer_pin')
                    ->find($this->account_id);

                if (!$account) {
                    return null;
                }

                return [
                    'id' => $account->id,
                    'character_name' => $account->character_name,
                    'username' => $account->username,
                    'password' => $account->password,
                    'transfer_pin' => $account->transfer_pin,
                ];

            case 'avatar':
                $account = Avatars::query()
                    ->select('id', 'username', 'password', 'transfer_pin')
                    ->find($this->account_id);

                if (!$account) {
                    return null;
                }

                return [
                    'id' => $account->id,
                    'username' => $account->username,
                    'password' => $account->password,
                    'transfer_pin' => $account->transfer_pin,
                ];

            default:
                return null;
        }
    }
}
