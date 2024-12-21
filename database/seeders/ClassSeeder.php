<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;

class ClassSeeder extends Seeder
{
    public function run()
    {
        $classes = [
            ['name' => 'Guerreiro'],
            ['name' => 'Mago'],
            ['name' => 'Arqueiro'],
            ['name' => 'Clérigo'],
        ];

        foreach ($classes as $class) {
            Classe::create($class);
        }
    }
}
