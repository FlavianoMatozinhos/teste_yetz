<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Classe;  // Assumindo que a tabela 'classes' esteja associada ao modelo 'Class'
use App\Models\Guild;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Cria roles e classes fictícias, caso não existam
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);

        $classGuerreiro = Classe::firstOrCreate(['name' => 'Guerreiro']);
        $classMago = Classe::firstOrCreate(['name' => 'Mago']);
        $classArqueiro = Classe::firstOrCreate(['name' => 'Arqueiro']);
        $classClerigo = Classe::firstOrCreate(['name' => 'Clérigo']);

        // Cria guildas fictícias, caso não existam
        $guild1 = Guild::firstOrCreate(['name' => 'Guilda A', 'max_players' => 3, 'min_players' => 0, 'creator_id' => 7]);
        $guild2 = Guild::firstOrCreate(['name' => 'Guilda B', 'max_players' => 1, 'min_players' => 1, 'creator_id' => 7]);
        $guild3 = Guild::firstOrCreate(['name' => 'Guilda C', 'max_players' => 0, 'min_players' => 0, 'creator_id' => 7]);

        // Cria usuários fictícios
        $users = [
            [
                'name' => 'Jogador 1',
                'email' => 'jogador1@example.com',
                'password' => Hash::make('password123'),  // Senha criptografada
                'role_id' => $roleAdmin->id,
                'class_id' => $classGuerreiro->id,
                'xp' => 10,
                'guild_id' => $guild1->id,
                'confirmed' => 1
            ],
            [
                'name' => 'Jogador 2',
                'email' => 'jogador2@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $roleUser->id,
                'class_id' => $classMago->id,
                'xp' => 80,
                'guild_id' => $guild1->id,
                'confirmed' => 1
            ],
            [
                'name' => 'Jogador 3',
                'email' => 'jogador3@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $roleUser->id,
                'class_id' => $classArqueiro->id,
                'xp' => 12,
                'guild_id' => $guild2->id,
                'confirmed' => 1
            ],
            [
                'name' => 'Jogador 4',
                'email' => 'jogador4@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $roleUser->id,
                'class_id' => $classClerigo->id,
                'xp' => 25,
                'guild_id' => $guild2->id,
                'confirmed' => 1
            ],
            [
                'name' => 'Jogador 5',
                'email' => 'jogador5@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $roleUser->id,
                'class_id' => $classGuerreiro->id,
                'xp' => 60,
                'guild_id' => $guild3->id,
                'confirmed' => 1
            ],
            [
                'name' => 'Jogador 6',
                'email' => 'jogador6@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $roleUser->id,
                'class_id' => $classMago->id,
                'xp' => 7,
                'guild_id' => $guild3->id,
                'confirmed' => 0  // Não confirmado
            ]
        ];

        // Inserir os dados dos usuários no banco de dados
        foreach ($users as $user) {
            User::create($user);
        }

        echo "Usuários criados com sucesso!";
    }
}