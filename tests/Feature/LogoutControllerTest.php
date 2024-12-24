<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\LogoutService;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    /** @test */
    public function it_logs_out_a_user_successfully()
    {
        // Criação do usuário mockado para evitar interação com o banco
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');

        // Mock do LogoutService
        $logoutServiceMock = Mockery::mock(LogoutService::class);
        $logoutServiceMock->shouldReceive('logoutUser')
                          ->with($user)
                          ->andReturn(['status' => 'success', 'message' => 'Logout realizado com sucesso.']);
        
        // Substitui a instância do LogoutService no container de dependências
        $this->app->instance(LogoutService::class, $logoutServiceMock);

        // Usando POST ao invés de GET
        $response = $this->actingAs($user)->post('/logout'); // Rota de logout

        // Verificando o status e a mensagem de sucesso
        $response->assertStatus(Response::HTTP_FOUND); // Status 302 de redirecionamento
        $response->assertSessionHas('success', 'Logout realizado com sucesso.');
    }

    /** @test */
    public function it_returns_error_on_logout_failure()
    {
        // Criação do usuário mockado para evitar interação com o banco
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');

        // Mock do LogoutService para falha
        $logoutServiceMock = Mockery::mock(LogoutService::class);
        $logoutServiceMock->shouldReceive('logoutUser')
                          ->with($user)
                          ->andReturn(['status' => 'error', 'message' => 'Erro ao realizar logout.']);
        
        // Substitui a instância do LogoutService no container de dependências
        $this->app->instance(LogoutService::class, $logoutServiceMock);

        // Usando POST ao invés de GET
        $response = $this->actingAs($user)->post('/logout'); // Rota de logout

        // Verificando o status de erro e a mensagem de falha
        $response->assertStatus(Response::HTTP_FOUND); // Status 302 de redirecionamento
        $response->assertSessionHasErrors(['error' => 'Erro ao realizar logout.']);
    }

    /** @test */
    public function it_returns_json_success_on_logout_for_api()
    {
        // Criação do usuário mockado para evitar interação com o banco
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');

        // Mock do LogoutService
        $logoutServiceMock = Mockery::mock(LogoutService::class);
        $logoutServiceMock->shouldReceive('logoutUser')
                          ->with($user)
                          ->andReturn(['status' => 'success', 'message' => 'Logout realizado com sucesso.']);
        
        // Substitui a instância do LogoutService no container de dependências
        $this->app->instance(LogoutService::class, $logoutServiceMock);

        // Usando POST ao invés de GET para API
        $response = $this->actingAs($user, 'api')->json('POST', '/logout'); // Usando API

        // Verificando o status e a mensagem de sucesso em formato JSON
        $response->assertStatus(Response::HTTP_OK); // Status 200 de sucesso
        $response->assertJson([
            'status' => 'success',
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

    /** @test */
    public function it_returns_error_json_on_logout_failure_for_api()
    {
        // Criação do usuário mockado para evitar interação com o banco
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');

        // Mock do LogoutService para falha
        $logoutServiceMock = Mockery::mock(LogoutService::class);
        $logoutServiceMock->shouldReceive('logoutUser')
                          ->with($user)
                          ->andReturn(['status' => 'error', 'message' => 'Erro ao realizar logout.']);
        
        // Substitui a instância do LogoutService no container de dependências
        $this->app->instance(LogoutService::class, $logoutServiceMock);

        // Usando POST ao invés de GET para API
        $response = $this->actingAs($user, 'api')->json('POST', '/logout'); // Usando API

        // Verificando o status de erro e a mensagem de falha em formato JSON
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR); // Status 500 de erro
        $response->assertJson([
            'status' => 'error',
            'message' => 'Erro ao realizar logout.'
        ]);
    }
}
