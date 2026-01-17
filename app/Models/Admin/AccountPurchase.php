<?php

namespace App\Models\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPurchase extends Model
{
    use SoftDeletes;
    protected $table = 'account_purchases';

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

    public function getPurchasedAtAttribute()
    {
        return $this->created_at->format('d-m-Y - H:i:s');
    }

    public function getAccountAttribute()
    {
        if (!$this->account_type || !$this->account_code) {
            return null;
        }

        $model = null;
        switch ($this->account_type) {
            case 'ninja':
                $model = Ninja::query()->select('id', 'code', 'username', 'password', 'transfer_pin');
                break;
            case 'avatar':
                $model = Avatar::query()->select('id', 'code', 'username', 'password', 'transfer_pin');
                break;
            default:
                $model = null;
                break;
        }
        if (!$model) {
            return null;
        }
        return $model->where('code', $this->account_code)->first();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
