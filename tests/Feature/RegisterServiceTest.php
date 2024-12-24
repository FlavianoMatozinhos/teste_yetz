<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Classe;
use App\Services\RegisterService;
use App\Repositories\ClassRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\GuildRepository;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    protected $registerService;
    protected $classRepository;
    protected $userRepository;
    protected $guildRepository;

    public function setUp(): void
    {
        parent::setUp();

        // Configuração do banco de dados SQLite em memória para testes
        $this->setUpDatabase();

        // Mock das dependências para não interagir com o banco real
        $this->classRepository = \Mockery::mock(ClassRepository::class);
        $this->userRepository = \Mockery::mock(PlayerRepository::class);
        $this->guildRepository = \Mockery::mock(GuildRepository::class);

        $this->registerService = new RegisterService(
            $this->classRepository,
            $this->userRepository,
            $this->guildRepository
        );
    }

    /** @test */
    public function it_should_register_a_user_successfully()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'class_id' => 1,  // ID mockado para classe
        ];

        // Mock para retornar algumas classes sem acessar o banco
        $this->classRepository
            ->shouldReceive('getAll')
            ->andReturn(collect([new Classe(['id' => 1, 'name' => 'Class 1'])]));  // Retorna um mock de classe

        // Mock para a criação do usuário
        $this->userRepository
            ->shouldReceive('createPlayer')
            ->andReturn(new User(['name' => 'John Doe', 'email' => 'john@example.com']));  // Mock do usuário

        // Teste da função de registro
        $result = $this->registerService->registerUser($userData);

        $this->assertEquals('error', $result['status']);
    }

    /** @test */
    public function it_should_return_validation_errors_when_registering_user()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'class_id' => 999,  // ID de classe inválido
        ];

        // Teste de erro de validação
        $result = $this->registerService->registerUser($userData);

        $this->assertEquals('error', $result['status']);
        $this->assertArrayHasKey('errors', $result);
    }

    /** @test */
    public function it_should_update_a_user_successfully()
    {
        $user = new User(['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com']);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        // Mock para buscar o usuário
        $this->userRepository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn($user);

        // Mock para atualizar o usuário
        $this->userRepository
            ->shouldReceive('updatePlayer')
            ->with(1, $updateData)
            ->andReturn(true);

        // Teste de atualização do usuário
        $result = $this->registerService->updateUser(1, $updateData);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Usuário atualizado com sucesso.', $result['data']['message']);
    }

    /** @test */
    public function it_should_delete_a_user_successfully()
    {
        $user = new User(['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com']);

        // Mock para buscar o usuário
        $this->userRepository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn($user);

        // Mock para deletar o usuário
        $this->userRepository
            ->shouldReceive('deletePlayer')
            ->with(1)
            ->andReturn(true);

        // Teste de exclusão do usuário
        $result = $this->registerService->deleteUser(1);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Usuário excluído com sucesso.', $result['data']['message']);
    }

    /** @test */
    public function it_should_return_error_when_deleting_non_existing_user()
    {
        $userId = 999;

        // Mock para retornar null ao buscar usuário
        $this->userRepository
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn(null);

        // Teste de erro ao tentar excluir um usuário inexistente
        $result = $this->registerService->deleteUser($userId);

        $this->assertEquals('error', $result['status']);
        $this->assertEquals('Usuário não encontrado.', $result['data']['message']);
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
