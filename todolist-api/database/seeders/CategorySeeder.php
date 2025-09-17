<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar um usuário de teste se não existir
        $user = \App\Models\User::firstOrCreate([
            'email' => 'admin@todolist.com'
        ], [
            'name' => 'Admin TodoList',
            'email' => 'admin@todolist.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now()
        ]);

        $categories = [
            'Trabalho',
            'Pessoal',
            'Estudos',
            'Casa',
            'Saúde',
            'Lazer'
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate([
                'name' => $categoryName,
                'user_id' => $user->id
            ]);
        }
    }
}
