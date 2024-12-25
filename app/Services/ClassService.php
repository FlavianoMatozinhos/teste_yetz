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
    public function getAllClasses(): array
    {
        try {
            return [
                'status' => 'success',
                'data' => $this->classRepository->getAll()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao listar classes.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Cria uma nova classe.
     */
    public function createClass(array $data): array
    {    
        try {
            $validator = validator($data, [
                'name' => 'required|string|max:255'
            ]);
    
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
    
            if ($this->classRepository->existsByName($data['name'])) {
                return [
                    'status' => 'error',
                    'message' => 'Já existe uma classe com esse nome.',
                    'status_code' => 400,
                    'data' => null
                ];
            }
    
            $newClass = $this->classRepository->create($data);
    
            return [
                'status' => 'success',
                'data' => $newClass
            ];
        } catch (ValidationException $e) {
            return [
                'status' => 'error',
                'message' => 'Dados inválidos.',
                'errors' => $e->errors(),
                'status_code' => 422,
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao criar classe.',
                'error' => $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ];
        }
    }
    

    /**
     * Retorna uma classe pelo ID.
     */
    public function getClassById($id): array
    {
        try {
            $class = $this->classRepository->findById($id);
            
            if (!$class) {
                return [
                    'status' => 'error',
                    'message' => 'Classe não encontrada.',
                    'status_code' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => $class
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao buscar classe.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Atualiza uma classe.
     */
    public function updateClass($id, array $data): array
    {
        try {
            $validator = validator($data, [
                'name' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (empty($data['name'])) {
                return [
                    'status' => 'error',
                    'message' => 'O nome da classe não pode estar vazio.',
                    'status_code' => 422
                ];
            }

            if ($this->classRepository->existsByName($data['name'], $id)) {
                return [
                    'status' => 'error',
                    'message' => 'Já existe uma classe com esse nome.',
                    'status_code' => 400
                ];
            }

            $updatedClass = $this->classRepository->update($id, $data);

            return [
                'status' => 'success',
                'data' => $updatedClass
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao atualizar classe.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Deleta uma classe.
     */
    public function deleteClass($id): array
    {
        try {
            $this->classRepository->delete($id);
            return ['status' => 'success', 'data' => null];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao excluir classe.',
                'error' => $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ];
        }
    }
    
}
