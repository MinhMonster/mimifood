<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarrotTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'carrot_transactions';

    protected $fillable = [
        'user_id',
        'game_type',
        'username',
        'server',
        'amount',
        'price',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
