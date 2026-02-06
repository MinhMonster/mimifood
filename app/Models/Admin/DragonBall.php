<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Account\AccountRelations;
use App\Traits\Account\AccountAttributes;
use App\Traits\Filterable;

class DragonBall extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AccountRelations;
    use AccountAttributes;
    use Filterable;

    protected $table = 'dragon_balls';

    protected $appends = ['active_discount', 'price', 'profit', 'account_type'];

    protected $fillable = [
        'code',
        'username',
        'password',
        'strength',
        'disciple',
        'images',
        'selling_price',
        'purchase_price',
        'discount_percent',
        'planet',
        'server',
        'type',
        'description',
        'is_sold',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'images' => 'array',
        'is_sold' => 'boolean',
        'is_family' => 'boolean',
        'is_full_image' => 'boolean',
        'selling_price' => 'decimal:0',
        'purchase_price' => 'decimal:0',
        'discount_percent' => 'decimal:0',
    ];

    /**
     * Default attributes.
     */
    protected $attributes = [
        'is_sold' => false,
    ];

    protected function filterableFields(): array
    {
        return [
            'id' => ['id'],
            'code' => ['code', 'like'],
            'username' => ['username', 'like'],
            'character_name' => ['character_name', 'like'],
            'server' => ['server'],
            'planet' => ['planet'],
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
        return 'dragon_ball';
    }
}
