<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlayerRepository
{
    public function getConfirmedPlayers()
    {
        return User::where('confirmed', true)->get();
    }

    public function getPlayersByClass($classId)
    {
        return User::where('class_id', $classId)->get();
    }

    public function getAllPlayers()
    {
        return User::all();
    }
    
    public function getAllWithConfirmationStatus()
    {
        return User::all()->map(function ($player) {
            $player->confirmed = $player->confirmed ? 'Sim' : 'Não';
            return $player;
        });
    }

    public function getPlayersByXP()
    {
        return User::orderBy('xp', 'desc')->get();
    }

    public function createPlayer(array $data)
    {
        DB::beginTransaction();

        try {
            $role = Role::where('name', 'player')->firstOrFail();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $role->id,
                'class_id' => $data['class_id'],
                'xp' => 0,
                'confirmed' => false,
            ]);

            DB::commit();

            return ['status' => 'success', 'user' => $user];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'errors' => ['general' => $e->getMessage()]];
        }
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
