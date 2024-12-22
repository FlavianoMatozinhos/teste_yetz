<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    protected $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * Lista todas as classes.
     */
    public function index()
    {
        return response()->json($this->classService->getAllClasses(), 200);
    }

    /**
     * Cria uma nova classe.
     */
    public function store(Request $request)
    {
        $result = $this->classService->createClass($request->all());

        return response()->json(
            $result['data'],
            $result['status'] === 'success' ? 201 : $result['status_code']
        );
    }

    /**
     * Exibe uma classe específica.
     */
    public function show($id)
    {
        $result = $this->classService->getClassById($id);

        return response()->json(
            $result['data'],
            $result['status'] === 'success' ? 200 : $result['status_code']
        );
    }

    /**
     * Atualiza uma classe específica.
     */
    public function update(Request $request, $id)
    {
        $result = $this->classService->updateClass($id, $request->all());

        return response()->json(
            $result['data'],
            $result['status'] === 'success' ? 200 : $result['status_code']
        );
    }

    /**
     * Deleta uma classe específica.
     */
    public function destroy($id)
    {
        $result = $this->classService->deleteClass($id);

        return response()->json(
            $result['data'],
            $result['status'] === 'success' ? 204 : $result['status_code']
        );
    }
}
