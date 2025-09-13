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
        $categories = [
            'Trabalho',
            'Pessoal',
            'Estudos',
            'Casa',
            'Saúde',
            'Lazer'
        ];

        // Buscar o primeiro usuário criado
        $user = \App\Models\User::first();
        
        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'user_id' => $user->id
            ]);
        }
    }
}
