<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Classe;
use App\Services\ClassService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class ClassControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        // Garantir que as variáveis de ambiente de teste sejam carregadas
        \Dotenv\Dotenv::createImmutable(base_path(), '.env.teste')->load();

        // Criar um usuário autenticado para os testes
        $user = User::factory()->create();

        // Criar algumas classes com a factory para testar
        Classe::factory()->count(2)->create();

        // Mock do serviço
        $mock = Mockery::mock(ClassService::class);
        $mock->shouldReceive('getAllClasses')
        ->once()
        ->andReturn([
            'status' => 'success',
            'data' => [
                ['name' => 'Class 1'],
                ['name' => 'Class 2'],
            ],
        ]);

        // Substituindo a implementação real do serviço no container
        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método index com o usuário autenticado
        $response = $this->actingAs($user, 'api')->getJson('/api/classes');

        // Verificar a resposta
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Classes listadas com sucesso.',
            'data' => true,  // Garantir que a estrutura de "data" existe
        ]);

        // Verifique se as classes estão sendo retornadas corretamente
        $response->assertJsonFragment(['name' => 'Class 1']);
        $response->assertJsonFragment(['name' => 'Class 2']);
    }

    public function testStore()
    {
        // Garantir que as variáveis de ambiente de teste sejam carregadas
        \Dotenv\Dotenv::createImmutable(base_path(), '.env.teste')->load();

        // Criar um usuário autenticado para os testes
        $user = User::factory()->create();

        // Mock do serviço
        $mock = Mockery::mock(ClassService::class);
        $mock->shouldReceive('createClass')
            ->once()
            ->with(['name' => 'New Class'])
            ->andReturn([
                'status' => 'success',
                'data' => ['name' => 'New Class'],
            ]);

        // Substituindo a implementação real do serviço no container
        $this->app->instance(ClassService::class, $mock);

        // Dados para a criação da classe
        $data = ['name' => 'New Class'];

        // Requisição para o método store com o usuário autenticado
        $response = $this->actingAs($user, 'api')->postJson('/api/classes', $data);

        // Verificar a resposta
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Classe criada com sucesso.',
            'data' => ['name' => 'New Class'],
        ]);
    }
    
    public function testShow()
    {
        // Garantir que as variáveis de ambiente de teste sejam carregadas
        \Dotenv\Dotenv::createImmutable(base_path(), '.env.teste')->load();

        // Criar um usuário autenticado para os testes
        $user = User::factory()->create();

        // Criar uma classe para o teste
        $classe = Classe::factory()->create();

        // Mock do serviço
        $mock = Mockery::mock(ClassService::class);
        $mock->shouldReceive('getClassById')
            ->once()
            ->with($classe->id)
            ->andReturn([
                'status' => 'success',
                'data' => ['id' => $classe->id, 'name' => $classe->name],
            ]);

        // Substituindo a implementação real do serviço no container
        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método show com o usuário autenticado
        $response = $this->actingAs($user, 'api')->getJson("/api/classes/{$classe->id}");

        // Verificar a resposta
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Classe encontrada com sucesso.',
            'data' => [
                'id' => $classe->id,
                'name' => $classe->name,
            ],
        ]);
    }

    public function testUpdate()
    {
        // Garantir que as variáveis de ambiente de teste sejam carregadas
        \Dotenv\Dotenv::createImmutable(base_path(), '.env.teste')->load();

        // Criar um usuário autenticado para os testes
        $user = User::factory()->create();

        // Criar uma classe para o teste
        $classe = Classe::factory()->create();

        // Mock do serviço
        $mock = Mockery::mock(ClassService::class);
        $mock->shouldReceive('updateClass')
            ->once()
            ->with($classe->id, ['name' => 'Updated Class'])
            ->andReturn([
                'status' => 'success',
                'data' => ['name' => 'Updated Class'],
            ]);

        // Substituindo a implementação real do serviço no container
        $this->app->instance(ClassService::class, $mock);

        // Dados para atualização da classe
        $data = ['name' => 'Updated Class'];

        // Requisição para o método update com o usuário autenticado
        $response = $this->actingAs($user, 'api')->putJson("/api/classes/{$classe->id}", $data);

        // Verificar a resposta
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Classe atualizada com sucesso.',
            'data' => ['name' => 'Updated Class'],
        ]);
    }

    public function testDestroy()
    {
        // Garantir que as variáveis de ambiente de teste sejam carregadas
        \Dotenv\Dotenv::createImmutable(base_path(), '.env.teste')->load();

        // Criar um usuário autenticado para os testes
        $user = User::factory()->create();

        // Criar uma classe para o teste
        $classe = Classe::factory()->create();

        // Mock do serviço
        $mock = Mockery::mock(ClassService::class);
        $mock->shouldReceive('deleteClass')
            ->once()
            ->with($classe->id)
            ->andReturn([
                'status' => 'success',
                'message' => 'Classe deletada com sucesso.',
            ]);

        // Substituindo a implementação real do serviço no container
        $this->app->instance(ClassService::class, $mock);

        // Requisição para o método destroy com o usuário autenticado
        $response = $this->actingAs($user, 'api')->deleteJson("/api/classes/{$classe->id}");

        // Verificar a resposta
        $response->assertStatus(204);
    }
}
