<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $classes = [
            ['name' => 'mestre'],
            ['name' => 'player'],
        ];

        foreach ($classes as $class) {
            Role::create($class);
        }
    }
}
