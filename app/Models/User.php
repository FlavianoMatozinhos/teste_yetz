<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;  // Adicionando o trait do Passport

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;  // Adicionando o trait HasApiTokens

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'class_id', 'xp', 'guild_id', 'confirmed',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }
}
