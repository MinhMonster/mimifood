<?php

namespace App\Models\Admin;

use App\Models\AccountPurchaseHistory;
use Illuminate\Http\Request;


class AdminAccountPurchaseHistory extends AccountPurchaseHistory
{
    protected $table = 'account_purchase_histories';

    /**
     * Hiển thị thêm field cho admin
     */
    protected $hidden = []; // bỏ ẩn user_id

    protected $appends = [
        'purchased_at',
        'account',
    ];

    /**
     * Admin được phép mass assign nhiều hơn
     */
    protected $fillable = [
        'account_type',
        'account_code',
        'user_id',
        'purchase_price',
        'selling_price',
        'note',
        'images',
        'created_at',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    /**
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $input = json_decode($request->input('input', '{}'));
        $status = $input->status ?? 'active';

        return $query->where(function ($q) use ($input) {
            if (isset($input->id) && ctype_digit((string) $input->id)) {
                $q->orWhere('id', $input->id);
            }

            if (!empty($input->code)) {
                $q->orWhere('code', $input->code);
            }

            if (!empty($input->username)) {
                $q->orWhere('username', 'like', "%{$input->username}%");
            }

            if (!empty($input->character_name)) {
                $q->orWhere('character_name', 'like', "%{$input->character_name}%");
            }
        });
    }

    public function getAccountAttribute()
    {
        if (!$this->account_type || !$this->account_code) {
            return null;
        }

        $model = null;
        switch ($this->account_type) {
            case 'ninja':
                $model = Ninjas::query()->select('id', 'code', 'username', 'password', 'transfer_pin');
                break;
            case 'avatar':
                $model = Avatars::query()->select('id', 'code', 'username', 'password', 'transfer_pin');
                break;
            default:
                $model = null;
                break;
        }
        return $model ? $model->where('code', $this->account_code)->first() : null;
    }
}
