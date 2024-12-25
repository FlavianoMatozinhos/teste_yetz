<?php

namespace App\Repositories;

use App\Models\Classe;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ClassRepository
{
    public function getAll()
    {
        return Classe::all();
    }

    public function create(array $data)
    {
        return Classe::create($data);
    }

    public function findById($id)
    {
        return Classe::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $class = Classe::findOrFail($id);
        $class->update($data);
        return $class;
    }

    public function delete($id): void
    {
        User::where('class_id', $id)->update(['class_id' => null]);
    
        $class = Classe::findOrFail($id);
        $class->delete();
    }
    public function existsByName($name)
    {
        $query = Classe::where('name', $name);

        return $query->exists();
    }
}
