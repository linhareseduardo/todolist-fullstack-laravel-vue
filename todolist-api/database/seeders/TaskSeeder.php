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
        $trabalhoCategory = Category::where('name', 'Trabalho')->first();
        $pessoalCategory = Category::where('name', 'Pessoal')->first();
        $estudosCategory = Category::where('name', 'Estudos')->first();

        $tasks = [
            [
                'category_id' => $trabalhoCategory->id,
                'title' => 'Finalizar relatório mensal',
                'description' => 'Completar e revisar o relatório de vendas do mês de agosto',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => '2025-09-15'
            ],
            [
                'category_id' => $trabalhoCategory->id,
                'title' => 'Reunião com equipe',
                'description' => 'Reunião semanal para alinhamento de projetos',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => '2025-09-12'
            ],
            [
                'category_id' => $pessoalCategory->id,
                'title' => 'Comprar presente de aniversário',
                'description' => 'Escolher e comprar presente para aniversário da Maria',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => '2025-09-20'
            ],
            [
                'category_id' => $estudosCategory->id,
                'title' => 'Estudar Laravel 11',
                'description' => 'Completar curso de Laravel e fazer projeto prático',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => '2025-09-30'
            ],
            [
                'category_id' => $pessoalCategory->id,
                'title' => 'Exercitar-se',
                'description' => 'Ir à academia pelo menos 3x esta semana',
                'status' => 'done',
                'priority' => 'medium',
                'due_date' => null
            ]
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
