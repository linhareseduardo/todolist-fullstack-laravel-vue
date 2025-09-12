<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_simple_api_test_endpoint(): void
    {
        $response = $this->getJson('/api/test-simple');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'API funcionando!'
                ]);
    }

    public function test_jwt_test_endpoint(): void
    {
        // Primeiro, criar um usuário para que o teste funcione
        $user = User::factory()->create();

        $response = $this->getJson('/api/test-jwt');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'token'
                ])
                ->assertJson([
                    'message' => 'JWT funcionando!'
                ]);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_jwt_test_endpoint_without_users(): void
    {
        // Teste quando não há usuários no banco
        $response = $this->getJson('/api/test-jwt');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Nenhum usuário encontrado'
                ]);
    }

    public function test_authenticated_user_can_get_user_info_via_middleware(): void
    {
        $auth = $this->authenticatedUser();

        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/user');

        // Aceita tanto 200 quanto 201 (pode variar conforme a versão do Laravel)
        $this->assertTrue(in_array($response->status(), [200, 201]),
            'Expected status 200 or 201, got ' . $response->status());

        $response
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ])
                ->assertJson([
                    'id' => $auth['user']->id,
                    'name' => $auth['user']->name,
                    'email' => $auth['user']->email
                ]);
    }

    public function test_unauthenticated_user_cannot_get_user_info_via_middleware(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_timezone_test_endpoint(): void
    {
        $response = $this->getJson('/api/test/timezone');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'config_timezone',
                        'php_timezone',
                        'current_time',
                        'current_time_formatted',
                        'current_time_iso',
                        'carbon_locale'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Teste de configuração de timezone'
                ]);

        // Verificar se os dados de timezone estão presentes
        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData['config_timezone']);
        $this->assertNotEmpty($responseData['php_timezone']);
        $this->assertNotEmpty($responseData['current_time']);
        $this->assertNotEmpty($responseData['current_time_formatted']);
        $this->assertNotEmpty($responseData['current_time_iso']);
        $this->assertNotEmpty($responseData['carbon_locale']);
    }
}
