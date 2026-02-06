<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;
use App\Traits\Filterable;

class Avatar extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AccountRelations;
    use AccountAttributes;
    use Filterable;

    protected $appends = ['active_discount', 'price', 'profit', 'account_type'];

    protected $fillable = [
        'code',
        'username',
        'password',
        'transfer_pin',
        'description',
        'images',
        'is_full_image',
        'selling_price',
        'purchase_price',
        'discount_percent',
        'land',
        'pets',
        'fish',
        'sex'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    protected function filterableFields(): array
    {
        return [
            'id' => ['id'],
            'code' => ['code', 'like'],
            'username' => ['username', 'like'],
            'sex' => ['sex'],
            'cash' => ['selling_price', 'range'],
            'is_sold' => ['is_sold'],
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

    public function getAccountTypeAttribute()
    {
        return 'avatar';
    }
}
