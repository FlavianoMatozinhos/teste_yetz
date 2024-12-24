<?php

namespace Tests\Feature;

use App\Services\ClassService;
use Tests\TestCase;

class ClassControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testIndex()
    {
        // Mock do serviço
        $mock = \Mockery::mock(ClassService::class);
        $mock->shouldReceive('getAllClasses')
            ->once()
            ->andReturn([
                'status' => 'success',
                'data' => [
                    ['id' => 1, 'name' => 'Class 1'],
                    ['id' => 2, 'name' => 'Class 2'],
                ],
            ]);
    
        $this->app->instance(ClassService::class, $mock);
    
        // Requisição para o método index
        $response = $this->getJson('/api/classes');
    
        // Verificar a resposta
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Classes listadas com sucesso.',
            'data' => [
                ['id' => 1, 'name' => 'Class 1'],
                ['id' => 2, 'name' => 'Class 2'],
            ],
        ]);
    }

    public function testStore()
    {
        $data = [
            'name' => 'New Class',
        ];

        // Mock do serviço
        $mock = \Mockery::mock(ClassService::class);
        $mock->shouldReceive('createClass')
            ->once()
            ->with($data)
            ->andReturn([
                'status' => 'success',
                'data' => $data,
            ]);

        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método store
        $response = $this->postJson('/api/classes', $data);

        // Verificar a resposta
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Classe criada com sucesso.',
            'data' => $data,
        ]);
    }

    public function testShow()
    {
        $classId = 1;

        // Mock do serviço
        $mock = \Mockery::mock(ClassService::class);
        $mock->shouldReceive('getClassById')
            ->once()
            ->with($classId)
            ->andReturn([
                'status' => 'success',
                'data' => ['id' => $classId, 'name' => 'Class 1'],
            ]);

        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método show
        $response = $this->getJson('/api/classes/' . $classId);

        // Verificar a resposta
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Classe encontrada com sucesso.',
            'data' => ['id' => $classId, 'name' => 'Class 1'],
        ]);
    }

    public function testUpdate()
    {
        $classId = 1;
        $data = [
            'name' => 'Updated Class',
        ];

        // Mock do serviço
        $mock = \Mockery::mock(ClassService::class);
        $mock->shouldReceive('updateClass')
            ->once()
            ->with($classId, $data)
            ->andReturn([
                'status' => 'success',
                'data' => $data,
            ]);

        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método update
        $response = $this->putJson('/api/classes/' . $classId, $data);

        // Verificar a resposta
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Classe atualizada com sucesso.',
            'data' => $data,
        ]);
    }

    public function testDestroy()
    {
        $classId = 1;

        // Mock do serviço
        $mock = \Mockery::mock(ClassService::class);
        $mock->shouldReceive('deleteClass')
            ->once()
            ->with($classId)
            ->andReturn([
                'status' => 'success',
            ]);

        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método destroy
        $response = $this->deleteJson('/api/classes/' . $classId);

        // Verificar a resposta
        $response->assertStatus(204);
    }
}
