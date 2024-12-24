<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Classe;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // senha fictícia
            'role_id' => Role::factory(), // Cria uma role falsa
            'class_id' => Classe::factory(), // Cria uma classe falsa
            'xp' => $this->faker->numberBetween(0, 100),
            'guild_id' => null, // Pode ser opcional
            'confirmed' => true,
        ];
    }
}