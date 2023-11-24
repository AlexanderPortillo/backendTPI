<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'user_name',
        'age',
        'gender',
        'img_profile',
        'email',
        'password',
        'country',
        'main_address',
        'shipping_address',
        'rol',
        'referral_link',
        'money_reffer',
        'discount_percentage',
        'remember_token',
    ];

    protected $hidden = [
        'rol',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}


