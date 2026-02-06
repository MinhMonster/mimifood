<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;
use App\Traits\Filterable;


class Ninja extends Model
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
        'character_name',
        'transfer_pin',
        'description',
        'images',
        'is_full_image',
        'selling_price',
        'purchase_price',
        'discount_percent',
        'class',
        'level',
        'server',
        'weapon',
        'type',
        'tl_1',
        'tl_2',
        'tl_3',
        'tl_4',
        'tl_5',
        'tl_6',
        'tl_7',
        'tl_8',
        'tl_9',
        'tl_10',
        'tl_11',
        'tl_12',
        'item_1',
        'item_2',
        'item_3',
        'item_4',
        'item_5',
        'item_6',
        'item_7',
        'item_8',
        'item_9',
        'item_10',
        'item_11',
        'item_12',
        'item_13',
    ];

    protected function filterableFields(): array
    {
        return [
            'id' => ['id'],
            'code' => ['code', 'like'],
            'username' => ['username', 'like'],
            'character_name' => ['character_name', 'like'],
            'server' => ['server'],
            'class' => ['class'],
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

    /**
     *
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
    ];

    public function getAccountTypeAttribute()
    {
        return 'ninja';
    }
}
