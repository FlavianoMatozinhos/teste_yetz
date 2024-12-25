<?php

namespace Database\Factories;

use App\Models\Guild;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuildFactory extends Factory
{
    protected $model = Guild::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'max_players' => $this->faker->numberBetween(10, 50),
            'min_players' => $this->faker->numberBetween(1, 10),
            'user_id' => User::factory(),
        ];
    }
}