<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('tasks')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categorias listadas com sucesso'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name'
            ]);

            $category = Category::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoria criada com sucesso'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::with('tasks')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Categoria encontrada'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id
            ]);

            $category->update([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoria atualizada com sucesso'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        // Verificar se há tarefas associadas
        if ($category->tasks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir categoria com tarefas associadas'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoria excluída com sucesso'
        ]);
    }
}
