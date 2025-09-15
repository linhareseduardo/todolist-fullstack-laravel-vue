<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Create an authenticated user for testing
     */
    protected function authenticatedUser(): array
    {
        $user = User::factory()->create();
        $token = auth()->guard('api')->login($user);

        return [
            'user' => $user,
            'token' => $token,
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ];
    }

    /**
     * Create a user and get JWT token
     */
    protected function actingAsUser(User $user = null): array
    {
        $user = $user ?: User::factory()->create();

        // Usar JWTAuth diretamente para garantir token único por usuário
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ];
    }

    /**
     * Make an authenticated request
     */
    protected function authenticatedRequest(string $method, string $uri, array $data = [], User $user = null)
    {
        $auth = $this->actingAsUser($user);

        return $this->withHeaders($auth['headers'])
                    ->json($method, $uri, $data);
    }

    /**
     * Get JSON headers
     */
    protected function jsonHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }
}
