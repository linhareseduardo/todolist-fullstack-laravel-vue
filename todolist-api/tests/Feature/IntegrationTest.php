<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_todo_workflow(): void
    {
        // 1. Registrar usuário
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201);
        $token = $response->json('data.token');
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 2. Criar categoria
        $categoryResponse = $this->withHeaders($headers)
                                 ->postJson('/api/categories', [
                                     'name' => 'Trabalho'
                                 ]);

        $categoryResponse->assertStatus(201);
        $categoryId = $categoryResponse->json('data.id');

        // 3. Criar tarefa
        $taskResponse = $this->withHeaders($headers)
                             ->postJson('/api/tasks', [
                                 'title' => 'Finalizar projeto',
                                 'description' => 'Terminar o projeto da API',
                                 'category_id' => $categoryId,
                                 'priority' => 'high',
                                 'status' => 'pending',
                                 'due_date' => '2025-12-31'
                             ]);

        $taskResponse->assertStatus(201);
        $taskId = $taskResponse->json('data.id');

        // 4. Listar tarefas
        $listResponse = $this->withHeaders($headers)
                             ->getJson('/api/tasks');

        $listResponse->assertStatus(200)
                    ->assertJsonCount(1, 'data');

        // 5. Atualizar status da tarefa
        $statusResponse = $this->withHeaders($headers)
                               ->patchJson("/api/tasks/{$taskId}/status", [
                                   'status' => 'done'
                               ]);

        $statusResponse->assertStatus(200)
                      ->assertJson([
                          'data' => ['status' => 'done']
                      ]);

        // 6. Verificar que a tarefa foi atualizada
        $updatedListResponse = $this->withHeaders($headers)
                                    ->getJson('/api/tasks');

        $updatedListResponse->assertStatus(200)
                           ->assertJsonFragment([
                               'status' => 'done'
                           ]);

        // 7. Fazer logout
        $logoutResponse = $this->withHeaders($headers)
                               ->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200);
    }

    public function test_user_isolation_with_real_data(): void
    {
        // Test user isolation simulating real data structure

        // Create users similar to your real database
        $eduardo = User::factory()->create([
            'name' => 'eduardo',
            'email' => 'eduardo@gmail.com',
            'password' => bcrypt('123456789')
        ]);

        $mailUser = User::factory()->create([
            'name' => 'teste',
            'email' => 'mail@mail.com.br',
            'password' => bcrypt('123456789')
        ]);

        // Create categories for Eduardo (simulating id: 25)
        $eduardoCategory = Category::factory()->create([
            'name' => 'Trabalho',
            'user_id' => $eduardo->id
        ]);

        // Create categories for Mail User (simulating ids: 21, 22, 23, 24)
        $mailCategories = [
            Category::factory()->create(['name' => 'categoria 1', 'user_id' => $mailUser->id]),
            Category::factory()->create(['name' => 'categoria 2', 'user_id' => $mailUser->id]),
            Category::factory()->create(['name' => 'categoria 3', 'user_id' => $mailUser->id]),
            Category::factory()->create(['name' => 'Trabalho', 'user_id' => $mailUser->id])
        ];

        // Create task for Eduardo (simulating id: 26)
        $eduardoTask = Task::factory()->create([
            'title' => 'teste',
            'description' => 'teste',
            'category_id' => $eduardoCategory->id,
            'user_id' => $eduardo->id,
            'status' => 'in_progress',
            'priority' => 'high'
        ]);

        // Create multiple tasks for Mail User
        $mailTasks = [];
        foreach ($mailCategories as $category) {
            $mailTasks[] = Task::factory()->create([
                'category_id' => $category->id,
                'user_id' => $mailUser->id
            ]);
        }

        // Create additional tasks for Mail User
        for ($i = 0; $i < 3; $i++) {
            $mailTasks[] = Task::factory()->create([
                'category_id' => $mailCategories[0]->id, // Use first category
                'user_id' => $mailUser->id
            ]);
        }

        // Eduardo should only see his own categories
        $response = $this->actingAs($eduardo, 'api')->getJson('/api/categories');
        $response->assertStatus(200);
        $eduardoCategoriesResponse = $response->json('data');

        // Eduardo should have only 1 category (Trabalho)
        $this->assertCount(1, $eduardoCategoriesResponse);
        $this->assertEquals('Trabalho', $eduardoCategoriesResponse[0]['name']);
        $this->assertEquals($eduardoCategory->id, $eduardoCategoriesResponse[0]['id']);

        // Mail user should see his own categories
        $response = $this->actingAs($mailUser, 'api')->getJson('/api/categories?per_page=10');
        $response->assertStatus(200);
        $mailCategoriesResponse = $response->json('data');

        // Mail user should have 4 categories
        $this->assertCount(4, $mailCategoriesResponse);
        $mailCategoryNames = collect($mailCategoriesResponse)->pluck('name')->toArray();
        $this->assertContains('categoria 1', $mailCategoryNames);
        $this->assertContains('categoria 2', $mailCategoryNames);
        $this->assertContains('categoria 3', $mailCategoryNames);
        $this->assertContains('Trabalho', $mailCategoryNames);

        // Eduardo should only see his own tasks
        $response = $this->actingAs($eduardo, 'api')->getJson('/api/tasks');
        $response->assertStatus(200);
        $eduardoTasksResponse = $response->json('data');

        // Eduardo should have only 1 task
        $this->assertCount(1, $eduardoTasksResponse);
        $this->assertEquals('teste', $eduardoTasksResponse[0]['title']);
        $this->assertEquals($eduardoTask->id, $eduardoTasksResponse[0]['id']);

        // Mail user should see his own tasks
        $response = $this->actingAs($mailUser, 'api')->getJson('/api/tasks?per_page=10');
        $response->assertStatus(200);
        $mailTasksResponse = $response->json('data');

        // Mail user should have multiple tasks (7 total: 4 from categories + 3 additional)
        $this->assertCount(7, $mailTasksResponse);

        // All tasks should belong to mail user
        foreach ($mailTasksResponse as $task) {
            $this->assertEquals($mailUser->id, $task['user_id']);
        }

        // Test cross-user access attempts (should fail)

        // Eduardo tries to access Mail user's first category
        $firstMailCategory = $mailCategories[0];
        $response = $this->actingAs($eduardo, 'api')->getJson("/api/categories/{$firstMailCategory->id}");
        $response->assertStatus(404);

        // Mail user tries to access Eduardo's task
        $response = $this->actingAs($mailUser, 'api')->getJson("/api/tasks/{$eduardoTask->id}");
        $response->assertStatus(404);

        // Eduardo tries to update Mail user's second category
        $secondMailCategory = $mailCategories[1];
        $response = $this->actingAs($eduardo, 'api')->putJson("/api/categories/{$secondMailCategory->id}", [
            'name' => 'HACKED CATEGORY'
        ]);
        $response->assertStatus(404);

        // Mail user tries to delete Eduardo's task
        $response = $this->actingAs($mailUser, 'api')->deleteJson("/api/tasks/{$eduardoTask->id}");
        $response->assertStatus(404);

        // Eduardo tries to create task in Mail user's third category
        $thirdMailCategory = $mailCategories[2];
        $response = $this->actingAs($eduardo, 'api')->postJson('/api/tasks', [
            'title' => 'Hack Attempt',
            'description' => 'Trying to use another user category',
            'category_id' => $thirdMailCategory->id, // Mail user's category
            'priority' => 'high',
            'status' => 'pending'
        ]);
        $response->assertStatus(422); // Validation error - category doesn't belong to user

        // Mail user tries to update Eduardo's task using his own category (should still fail)
        $response = $this->actingAs($mailUser, 'api')->putJson("/api/tasks/{$eduardoTask->id}", [
            'title' => 'HACKED TASK',
            'description' => 'Trying to hack Eduardo task',
            'category_id' => $firstMailCategory->id, // Even using own category
            'priority' => 'high',
            'status' => 'done'
        ]);
        $response->assertStatus(404); // Task doesn't belong to mail user
    }

    public function test_category_deletion_cascades_to_tasks(): void
    {
        $auth = $this->authenticatedUser();

        // Criar categoria
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        // Criar tarefas na categoria
        Task::factory()->count(3)->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        // Verificar que existem 3 tarefas
        $this->assertDatabaseCount('tasks', 3);

        // Tentar deletar categoria (deve falhar por ter tarefas)
        $response = $this->withHeaders($auth['headers'])
                         ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Não é possível excluir categoria com tarefas associadas'
                ]);

        // Verificar que a categoria não foi deletada
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        // Verificar que as tarefas ainda existem
        $this->assertDatabaseCount('tasks', 3);
    }

    public function test_api_returns_proper_error_formats(): void
    {
        // Teste endpoint não autenticado
        $response = $this->getJson('/api/categories');
        $response->assertStatus(401)
                ->assertJsonStructure(['message']);

        // Teste validação
        $auth = $this->authenticatedUser();
        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/categories', ['name' => '']);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors' => ['name']
                ]);

        // Teste recurso não encontrado
        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/categories/99999');

        $response->assertStatus(404);
    }

    public function test_date_formatting_consistency(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        // Criar tarefa com data específica
        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/tasks', [
                             'title' => 'Test Date Format',
                             'category_id' => $category->id,
                             'priority' => 'medium',
                             'status' => 'pending',
                             'due_date' => '2025-12-25' // Formato ISO
                         ]);

        // Verificar que a API retorna no formato brasileiro sem hora
        $response->assertStatus(201)
                ->assertJsonPath('data.due_date.formatted', '25/12/2025');

        // Verificar na listagem também
        $listResponse = $this->withHeaders($auth['headers'])
                             ->getJson('/api/tasks');

        $listResponse->assertJsonPath('data.0.due_date.formatted', '25/12/2025');
    }
}
