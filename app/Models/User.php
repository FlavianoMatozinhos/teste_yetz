<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

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

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}
