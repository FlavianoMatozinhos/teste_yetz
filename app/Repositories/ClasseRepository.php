<?php

namespace App\Repositories;

use App\Models\Classe;

class ClasseRepository
{
    /**
     * Retorna todas as classes disponíveis.
     */
    public function getAll()
    {
        return Classe::all();
    }
}
