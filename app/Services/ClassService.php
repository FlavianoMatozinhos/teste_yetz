<?php

namespace App\Services;

use App\Repositories\ClassRepository;
use Illuminate\Validation\ValidationException;
use Exception;

class ClassService
{
    protected $classRepository;

    public function __construct(ClassRepository $classRepository)
    {
        $this->classRepository = $classRepository;
    }

    /**
     * Retorna todas as classes.
     */
    public function getAllClasses()
    {
        try {
            return [
                'status' => 'success',
                'data' => $this->classRepository->getAll()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Erro ao listar classes.', 'error' => $e->getMessage()],
                'status_code' => 500
            ];
        }
    }

    /**
     * Cria uma nova classe.
     */
    public function createClass(array $data)
    {
        try {
            $validator = validator($data, [
                'name' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return [
                'status' => 'success',
                'data' => $this->classRepository->create($data)
            ];
        } catch (ValidationException $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Dados inválidos.', 'errors' => $e->errors()],
                'status_code' => 422
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Erro ao criar classe.', 'error' => $e->getMessage()],
                'status_code' => 500
            ];
        }
    }

    /**
     * Retorna uma classe pelo ID.
     */
    public function getClassById($id)
    {
        try {
            return [
                'status' => 'success',
                'data' => $this->classRepository->findById($id)
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Classe não encontrada.', 'error' => $e->getMessage()],
                'status_code' => 404
            ];
        }
    }

    /**
     * Atualiza uma classe.
     */
    public function updateClass($id, array $data)
    {
        try {
            $validator = validator($data, [
                'name' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return [
                'status' => 'success',
                'data' => $this->classRepository->update($id, $data)
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Erro ao atualizar classe.', 'error' => $e->getMessage()],
                'status_code' => 500
            ];
        }
    }

    /**
     * Deleta uma classe.
     */
    public function deleteClass($id)
    {
        try {
            $this->classRepository->delete($id);
            return ['status' => 'success', 'data' => null];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Erro ao excluir classe.', 'error' => $e->getMessage()],
                'status_code' => 500
            ];
        }
    }
}
