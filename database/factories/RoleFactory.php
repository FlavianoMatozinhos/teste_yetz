<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * O nome do modelo que esta fábrica representa.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Defina o estado do modelo.
     *
     * @return array
     */
    public function definition()
    {
        $classes = [
            ['name' => 'mestre'],
            ['name' => 'player'],
        ];

        return $classes[array_rand($classes)];
    }
}
