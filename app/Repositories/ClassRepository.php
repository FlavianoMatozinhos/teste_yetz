<?php

namespace App\Repositories;

use App\Models\Classe;

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

    public function delete($id)
    {
        $class = Classe::findOrFail($id);
        $class->delete();
    }
}
