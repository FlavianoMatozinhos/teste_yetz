<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_players', 'min_players', 'creator_id'];

    public function players()
    {
        return $this->hasMany(User::class);
    }

    public function hasClass($className)
    {
        return $this->players()
                    ->whereHas('class', function ($query) use ($className) {
                        $query->where('name', $className);
                    })
                    ->exists();
    }
}
