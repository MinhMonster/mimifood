<?php

namespace App\Models\Admin;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPurchase extends Model
{
    use Filterable;
    use SoftDeletes;
    protected $table = 'account_purchases';

    /**
     * Hiển thị thêm field cho admin
     */
    protected $hidden = []; // bỏ ẩn user_id

    protected $appends = [
        'purchased_at',
        // 'account',
    ];

    /**
     * Admin được phép mass assign nhiều hơn
     */
    protected $fillable = [
        'account_type',
        'account_code',
        'account_id',
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

    protected $attributes = [
        'images' => [],
    ];

    protected function filterableFields(): array
    {
        return [
            'id' => ['id'],
            'code' => ['code', 'like'],
            'account_name' => ['account.username', 'like'],
            'account_code' => ['account.code', 'like'],
            'user_id' => ['user.id'],
            'user_name' => ['user.name', 'like'],
            'created_at' => ['created_at', 'date_range'],
        ];
    }

    public function getPurchasedAtAttribute()
    {
        return $this->created_at->format('d-m-Y - H:i:s');
    }

    public function account()
    {
        return $this->morphTo();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
