<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Category;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter o usuário de teste
        $user = \App\Models\User::where('email', 'admin@todolist.com')->first();
        
        if (!$user) {
            $this->command->error('Usuário admin@todolist.com não encontrado. Execute CategorySeeder primeiro.');
            return;
        }

        $trabalhoCategory = Category::where('name', 'Trabalho')->where('user_id', $user->id)->first();
        $pessoalCategory = Category::where('name', 'Pessoal')->where('user_id', $user->id)->first();
        $estudosCategory = Category::where('name', 'Estudos')->where('user_id', $user->id)->first();

        if (!$trabalhoCategory || !$pessoalCategory || !$estudosCategory) {
            $this->command->error('Categorias não encontradas. Execute CategorySeeder primeiro.');
            return;
        }

        $tasks = [
            [
                'category_id' => $trabalhoCategory->id,
                'title' => 'Finalizar relatório mensal',
                'description' => 'Completar e revisar o relatório de vendas do mês de agosto',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => '2025-09-15',
                'user_id' => $user->id
            ],
            [
                'category_id' => $trabalhoCategory->id,
                'title' => 'Reunião com equipe',
                'description' => 'Reunião semanal para alinhamento de projetos',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => '2025-09-12',
                'user_id' => $user->id
            ],
            [
                'category_id' => $pessoalCategory->id,
                'title' => 'Comprar presente de aniversário',
                'description' => 'Escolher e comprar presente para aniversário da Maria',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => '2025-09-20',
                'user_id' => $user->id
            ],
            [
                'category_id' => $estudosCategory->id,
                'title' => 'Estudar Laravel 11',
                'description' => 'Completar curso de Laravel e fazer projeto prático',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => '2025-09-30',
                'user_id' => $user->id
            ],
            [
                'category_id' => $pessoalCategory->id,
                'title' => 'Exercitar-se',
                'description' => 'Ir à academia pelo menos 3x esta semana',
                'status' => 'done',
                'priority' => 'medium',
                'due_date' => null,
                'user_id' => $user->id
            ]
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
