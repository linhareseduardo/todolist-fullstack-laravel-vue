<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_their_tasks(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        // Criar tarefas para o usuário autenticado
        Task::factory()->count(2)->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        // Criar tarefa para outro usuário (não deve aparecer)
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id', 'title', 'description', 'status',
                            'priority', 'due_date', 'category', 'user_id'
                        ]
                    ],
                    'message'
                ])
                ->assertJsonCount(2, 'data');
    }

    public function test_unauthenticated_user_cannot_list_tasks(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        $taskData = [
            'title' => 'Nova Tarefa',
            'description' => 'Descrição da tarefa',
            'category_id' => $category->id,
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => '2025-12-31'
        ];

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => ['id', 'title', 'description', 'status', 'priority', 'due_date', 'category'],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'title' => 'Nova Tarefa',
                        'description' => 'Descrição da tarefa',
                        'priority' => 'medium',
                        'status' => 'pending',
                        'user_id' => $auth['user']->id
                    ]
                ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Nova Tarefa',
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);
    }

    public function test_user_cannot_create_task_without_required_fields(): void
    {
        $auth = $this->authenticatedUser();

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/tasks', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['category_id', 'title']);
    }

    public function test_user_cannot_create_task_with_other_user_category(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/tasks', [
                             'title' => 'Tarefa Inválida',
                             'category_id' => $otherCategory->id,
                             'priority' => 'medium',
                             'status' => 'pending',
                             'due_date' => '2025-12-31'
                         ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['category_id']);
    }

    public function test_task_due_date_is_stored_as_date_only(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        $response = $this->withHeaders($auth['headers'])
                         ->postJson('/api/tasks', [
                             'title' => 'Test Date Task',
                             'category_id' => $category->id,
                             'priority' => 'medium',
                             'status' => 'pending',
                             'due_date' => '2025-12-31'
                         ]);

        $response->assertStatus(201);

        $task = Task::where('title', 'Test Date Task')->first();

        // Verificar que a data foi salva corretamente como date
        $this->assertEquals('2025-12-31', $task->due_date->format('Y-m-d'));

        // Verificar que a resposta da API formata a data corretamente (formato brasileiro)
        $responseData = $response->json('data');
        $this->assertIsArray($responseData['due_date']);
        $this->assertEquals('31/12/2025', $responseData['due_date']['formatted']);
    }

    public function test_authenticated_user_can_view_their_task(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);
        $task = Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => ['id', 'title', 'description', 'status', 'priority', 'due_date', 'category']
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'user_id' => $auth['user']->id
                    ]
                ]);
    }

    public function test_user_cannot_view_other_user_task(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_update_their_task(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);
        $task = Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        $updateData = [
            'title' => 'Tarefa Atualizada',
            'description' => 'Nova descrição',
            'category_id' => $category->id,
            'priority' => 'high',
            'status' => 'done',
            'due_date' => '2025-12-25'
        ];

        $response = $this->withHeaders($auth['headers'])
                         ->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $task->id,
                        'title' => 'Tarefa Atualizada',
                        'priority' => 'high',
                        'status' => 'done'
                    ]
                ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tarefa Atualizada',
            'priority' => 'high',
            'status' => 'done'
        ]);
    }

    public function test_user_can_update_task_status_only(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);
        $task = Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'status' => 'pending'
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->patchJson("/api/tasks/{$task->id}/status", [
                             'status' => 'done'
                         ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $task->id,
                        'status' => 'done'
                    ]
                ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done'
        ]);
    }

    public function test_user_cannot_update_other_user_task(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->putJson("/api/tasks/{$task->id}", [
                             'title' => 'Tentativa de Atualização'
                         ]);

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_delete_their_task(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);
        $task = Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_user_cannot_delete_other_user_task(): void
    {
        $auth = $this->authenticatedUser();
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_tasks_can_be_filtered_by_category(): void
    {
        $auth = $this->authenticatedUser();
        $category1 = Category::factory()->create(['user_id' => $auth['user']->id]);
        $category2 = Category::factory()->create(['user_id' => $auth['user']->id]);

        Task::factory()->count(2)->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category1->id
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category2->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson("/api/tasks?category_id={$category1->id}");

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
    }

    public function test_tasks_can_be_filtered_by_status(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        Task::factory()->count(2)->pending()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        Task::factory()->completed()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?status=pending');

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
    }

    public function test_tasks_can_be_searched_by_text(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'title' => 'Tarefa importante',
            'description' => 'Uma tarefa muito importante'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'title' => 'Outra tarefa',
            'description' => 'Descrição normal'
        ]);

        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?search=importante');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment([
                    'title' => 'Tarefa importante'
                ]);
    }

    public function test_tasks_can_be_filtered_by_priority(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        // Criar tarefas com diferentes prioridades
        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'priority' => 'high',
            'title' => 'Tarefa Alta Prioridade'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'priority' => 'low',
            'title' => 'Tarefa Baixa Prioridade'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'title' => 'Tarefa Média Prioridade'
        ]);

        // Testar filtro por prioridade alta
        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?priority=high');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment([
                    'title' => 'Tarefa Alta Prioridade',
                    'priority' => 'high'
                ]);

        // Testar filtro por prioridade baixa
        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?priority=low');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment([
                    'title' => 'Tarefa Baixa Prioridade',
                    'priority' => 'low'
                ]);
    }

    public function test_tasks_can_be_filtered_by_multiple_criteria(): void
    {
        $auth = $this->authenticatedUser();
        $category1 = Category::factory()->create(['user_id' => $auth['user']->id]);
        $category2 = Category::factory()->create(['user_id' => $auth['user']->id]);

        // Criar tarefas com diferentes combinações
        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category1->id,
            'status' => 'pending',
            'priority' => 'high',
            'title' => 'Tarefa Alvo'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category1->id,
            'status' => 'done',
            'priority' => 'high',
            'title' => 'Tarefa Status Diferente'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category2->id,
            'status' => 'pending',
            'priority' => 'high',
            'title' => 'Tarefa Categoria Diferente'
        ]);

        // Testar filtro combinado: categoria + status + prioridade
        $response = $this->withHeaders($auth['headers'])
                         ->getJson("/api/tasks?category_id={$category1->id}&status=pending&priority=high");

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment([
                    'title' => 'Tarefa Alvo',
                    'status' => 'pending',
                    'priority' => 'high'
                ]);
    }

    public function test_tasks_can_be_filtered_by_all_status_types(): void
    {
        $auth = $this->authenticatedUser();
        $category = Category::factory()->create(['user_id' => $auth['user']->id]);

        // Criar tarefas com diferentes status
        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'status' => 'pending',
            'title' => 'Tarefa Pendente'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'status' => 'in_progress',
            'title' => 'Tarefa Em Progresso'
        ]);

        Task::factory()->create([
            'user_id' => $auth['user']->id,
            'category_id' => $category->id,
            'status' => 'done',
            'title' => 'Tarefa Concluída'
        ]);

        // Testar filtro por status: pending
        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?status=pending');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['title' => 'Tarefa Pendente']);

        // Testar filtro por status: in_progress
        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?status=in_progress');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['title' => 'Tarefa Em Progresso']);

        // Testar filtro por status: done
        $response = $this->withHeaders($auth['headers'])
                         ->getJson('/api/tasks?status=done');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['title' => 'Tarefa Concluída']);
    }
}
