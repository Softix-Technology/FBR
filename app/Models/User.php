<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'is_admin',
        'name',
        'username',
        'email',
        'password',
        'fbr_access_token',
        'use_sandbox',
        'cinc_ntn',
        'strn',
        'address',
        'business_name',
        'province',
        'c_id',
        'reference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'use_sandbox' => 'boolean',
    ];

    // Example relation
    public function buyers(): HasMany
    {
        return $this->hasMany(Buyer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'c_id', 'cid');
    }
}