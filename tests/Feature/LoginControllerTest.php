<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Services\LoginService;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{

    /** @test */
    public function it_logs_in_user_successfully()
    {
        // Usando SQLite em memória para evitar a interferência com o banco real
        $this->setUpDatabase(); // Configuração do banco de dados mockado

        // Cria um usuário utilizando a factory
        $user = User::factory()->create(); 

        // Mock do LoginService
        $loginServiceMock = Mockery::mock(LoginService::class);
        $loginServiceMock->shouldReceive('login')
                          ->with(['email' => $user->email, 'password' => 'password123'])
                          ->andReturn(['status' => 'success', 'token' => 'dummy_token']);

        $this->app->instance(LoginService::class, $loginServiceMock);

        // Simula o envio de uma requisição de login
        $response = $this->json('POST', '/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_OK); // Status 200 de sucesso
        $response->assertJson([
            'status' => 'success',
            'token' => 'dummy_token',
        ]);
    }

    /** @test */
    public function it_creates_role_without_duplicate_error()
    {
        // Usando SQLite em memória para evitar a interferência com o banco real
        $this->setUpDatabase(); // Configuração do banco de dados mockado

        // Mockando a inserção para não causar erro de duplicação
        $roleMock = Mockery::mock(Role::class);
        $roleMock->shouldReceive('create')
                 ->with(['name' => 'player'])
                 ->andReturnSelf(); // Retorna o próprio mock como se fosse o modelo salvo

        $this->app->instance(Role::class, $roleMock);

        // Simula a criação de um role
        $role = Role::create(['name' => 'player']);

        $this->assertEquals('player', $role->name); // Verifica se o role foi criado corretamente
    }

    protected function setUpDatabase()
    {
        // Configura o banco de dados SQLite em memória para os testes
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        
        // Reseta o banco de dados para garantir um estado limpo
        $this->artisan('migrate');
    }
}
