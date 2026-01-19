<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class WalletTransaction extends Model
{
    protected $casts = [
        'meta' => 'array',
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    // protected $appends = ['reference'];

    protected $hidden = ['user_id', 'updated_at', 'reference_type', 'reference_id'];

    protected $fillable = [
        'user_id',
        'direction',
        'type',
        'reference_type',
        'reference_id',
        'amount',
        'balance_before',
        'balance_after',
        'description',
    ];

    /**
     *
     * @param  Builder  $query
     * @param  Request  $request
     * @return Builder
     */
    public function scopeSearch($query, Request $request)
    {
        $input = json_decode($request->input('input', '{}'));
        return $query->where(function ($q) use ($input) {
            if (isset($input->user_id)) {
                $q->orWhere('user_id', $input->user_id);
            }
        });
    }

    /**
     * Quan hệ: Mỗi giao dịch thuộc về một user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDescriptionAttribute()
    {
        if ($this->type) {
            $content = config("transactions.types.{$this->type}.content");
            if ($this->reference_id) {
                return $content . " #{$this->reference_id}";
            }
            return $content;

            // return $config['content'] . ": " . (is_array($this->meta) ? $this->meta['id'] ?? null : null);
        }
        return $this->attributes['description'];
    }
}
