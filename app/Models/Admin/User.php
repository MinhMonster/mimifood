<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'cash',
        'status',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'    => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeSearch($query, Request $request)
    {
        $input = json_decode($request->input('input', '{}'));
        return $query->where(function ($q) use ($input) {
            if (!empty($input->id)) {
                $q->where('id', 'like', "%{$input->id}%");
            }
            if (!empty($input->name)) {
                $q->where('name', 'like', "%{$input->name}%");
            }
            if (!empty($input->email)) {
                $q->where('email', 'like', "%{$input->email}%");
            }
        });
    }
}
