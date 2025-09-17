<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Obter user ID diretamente do token JWT para evitar cache em testes
        $userId = auth()->guard('api')->user()->id;
        $query = Category::forUser($userId)
            ->withCount('tasks')
            ->orderBy('name');

        // Paginação - 3 itens por página por padrão
        $perPage = $request->get('per_page', 3);
        $categories = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories->items()),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
                'has_more_pages' => $categories->hasMorePages(),
                'prev_page_url' => $categories->previousPageUrl(),
                'next_page_url' => $categories->nextPageUrl()
            ],
            'message' => 'Categorias listadas com sucesso'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Obter user ID diretamente do token JWT para evitar cache em testes
            $userId = auth()->guard('api')->user()->id;

            $request->validate([
                'name' => "required|string|max:255|unique:categories,name,NULL,id,user_id,{$userId}"
            ]);

            $category = Category::create([
                'name' => $request->name,
                'user_id' => $userId
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
        $userId = auth()->guard('api')->user()->id;
        $category = Category::forUser($userId)->withCount('tasks')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Categoria encontrada'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $userId = auth()->guard('api')->user()->id;
        $category = Category::forUser($userId)->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id . ',id,user_id,' . $userId
            ]);

            $category->update([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category),
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
        $userId = auth()->guard('api')->user()->id;
        $category = Category::forUser($userId)->find($id);

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
