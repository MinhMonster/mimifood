<?php

namespace App\Models;

use App\Models\Ninja;
use App\Models\Avatar;
use App\Models\DragonBall;
use App\Traits\HidesTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPurchase extends Model
{
    use SoftDeletes;
    use HidesTimestamps;

    protected $appends = ['purchased_at', 'account'];
    protected $hidden = [
        'user_id',
        'images',
        'note',
        'purchase_price',
        'account_id',
    ];
    protected $fillable = [
        'account_type',
        'account_code',
        'account_id',
        'user_id',
        'selling_price',
        'purchase_price',
        'images',
        'note',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    protected $attributes = [
        'images' => null,
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

        $configs = [
            'ninja' => [
                'model' => Ninja::class,
                'fields' => ['code', 'character_name', 'username', 'password', 'transfer_pin'],
            ],
            'avatar' => [
                'model' => Avatar::class,
                'fields' => ['code', 'username', 'password', 'transfer_pin'],
            ],
            'dragon_ball' => [
                'model' => DragonBall::class,
                'fields' => ['code', 'username', 'password'],
            ],
        ];

        if (!isset($configs[$this->account_type])) {
            return null;
        }

        $config = $configs[$this->account_type];

        $account = $config['model']::query()
            ->select($config['fields'])
            ->where('code', $this->account_code)
            ->first();

        return $account ? $account->only($config['fields']) : null;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
