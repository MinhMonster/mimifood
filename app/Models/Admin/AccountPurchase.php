<?php

namespace App\Models\Admin;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\AccountPurchaseStatus;

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
            'deleted_at' => function (Builder $query, $value) {
                if ((int) $value === 1) {
                    // Deleted
                    $query->onlyTrashed();
                } elseif ((int) $value === 0) {
                    // Active
                    $query->whereNull('deleted_at');
                }
            },
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

    public function getStatusAttribute($value)
    {
        if (
            in_array($value, [
                AccountPurchaseStatus::DEPOSIT,
                AccountPurchaseStatus::INSTALLMENT_FIRST,
            ]) &&
            $this->deadline_at &&
            $this->deadline_at < now() &&
            $this->cancelled_at === null
        ) {
            return AccountPurchaseStatus::EXPIRED;
        }

        return $value;
    }
}
