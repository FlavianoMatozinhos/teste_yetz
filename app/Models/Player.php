<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'class_id', 'xp', 'guild_id', 'confirmed'];

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}
