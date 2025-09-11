<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_their_categories(): void
    {
        $auth = $this->authenticatedUser();
        
        // Criar categorias para o usuário autenticado
        $categories = Category::factory()->count(3)->create(['user_id' => $auth['user']->id]);
        
        // Criar categoria para outro usuário (não deve aparecer)
        $otherUser = User::factory()->create();
        Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/categories');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => ['id', 'name', 'user_id', 'tasks_count']
                    ],
                    'message'
                ])
                ->assertJsonCount(3, 'data'); // Apenas 3 categorias do usuário
    }

    public function test_unauthenticated_user_cannot_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_category(): void
    {
        $auth = $this->authenticatedUser();

        $categoryData = [
            'name' => 'Nova Categoria'
        ];

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => ['id', 'name', 'user_id'],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'Nova Categoria',
                        'user_id' => $auth['user']->id
                    ]
                ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Nova Categoria',
            'user_id' => $auth['user']->id
        ]);
    }

    public function test_user_cannot_create_category_without_name(): void
    {
        $auth = $this->authenticatedUser();

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/categories', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    public function test_user_cannot_create_category_with_duplicate_name(): void
    {
        $auth = $this->authenticatedUser();
        
        // Criar categoria existente para o mesmo usuário
        Category::factory()->create([
            'name' => 'Categoria Existente',
            'user_id' => $auth['user']->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/categories', [
                             'name' => 'Categoria Existente'
                         ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_create_category_with_same_name_as_other_user(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        
        // Outro usuário já tem categoria com esse nome
        Category::factory()->create([
            'name' => 'Categoria Comum',
            'user_id' => $otherUser->id
        ]);

        // Usuário atual deve conseguir criar categoria com mesmo nome
        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/categories', [
                             'name' => 'Categoria Comum'
                         ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseCount('categories', 2); // Duas categorias com mesmo nome
    }

    public function test_authenticated_user_can_view_their_category(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => ['id', 'name', 'user_id', 'tasks_count']
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'user_id' => $auth['user']->id
                    ]
                ]);
    }

    public function test_user_cannot_view_other_user_category(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson("/api/categories/{$category->id}");

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_update_their_category(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        $updateData = [
            'name' => 'Categoria Atualizada'
        ];

        $response = $this->withHeaders($auth['headers'])
                         ->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $category->id,
                        'name' => 'Categoria Atualizada',
                        'user_id' => $auth['user']->id
                    ]
                ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Categoria Atualizada',
            'user_id' => $auth['user']->id
        ]);
    }

    public function test_user_cannot_update_other_user_category(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->putJson("/api/categories/{$category->id}", [
                             'name' => 'Tentativa de Atualização'
                         ]);

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_delete_their_category(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    public function test_user_cannot_delete_other_user_category(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(404);
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id
        ]);
    }
}
