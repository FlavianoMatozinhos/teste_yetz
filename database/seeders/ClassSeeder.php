<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;

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
            ClassModel::create($class);
        }
    }
}
