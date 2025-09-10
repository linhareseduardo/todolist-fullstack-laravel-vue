<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove a constraint única global do campo name
            $table->dropUnique('categories_name_unique');
            
            // Adiciona uma constraint única composta para name + user_id
            // Isso permite que diferentes usuários tenham categorias com o mesmo nome
            $table->unique(['name', 'user_id'], 'categories_name_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove a constraint composta
            $table->dropUnique('categories_name_user_unique');
            
            // Restaura a constraint única global
            $table->unique('name', 'categories_name_unique');
        });
    }
};
