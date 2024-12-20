<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_players'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}

