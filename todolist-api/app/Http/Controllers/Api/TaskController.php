<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Category;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->guard('api')->user()->id;
        $query = Task::forUser($userId)->with('category');

        // Filtros opcionais
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Buscar por texto se especificado
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $query->orderBy('created_at', 'desc');

        // Paginação - 3 itens por página por padrão
        $perPage = $request->get('per_page', 3);
        $tasks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => TaskResource::collection($tasks->items()),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'last_page' => $tasks->lastPage(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
                'has_more_pages' => $tasks->hasMorePages(),
                'prev_page_url' => $tasks->previousPageUrl(),
                'next_page_url' => $tasks->nextPageUrl()
            ],
            'message' => 'Tarefas listadas com sucesso'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $userId = auth()->guard('api')->user()->id;

            $request->validate([
                'category_id' => 'required|exists:categories,id,user_id,' . $userId,
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|in:pending,in_progress,done',
                'priority' => 'nullable|in:low,medium,high',
                'due_date' => 'nullable|date|after_or_equal:today'
            ]);

            $task = Task::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status ?? 'pending',
                'priority' => $request->priority ?? 'medium',
                'due_date' => $request->due_date,
                'user_id' => $userId
            ]);

            $task->load('category');

            return response()->json([
                'success' => true,
                'data' => new TaskResource($task),
                'message' => 'Tarefa criada com sucesso'
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
        $task = Task::with('category')->where('user_id', $userId)->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TaskResource($task),
            'message' => 'Tarefa encontrada'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $userId = auth()->guard('api')->user()->id;
        $task = Task::where('user_id', $userId)->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada'
            ], 404);
        }

        try {
            $request->validate([
                'category_id' => 'sometimes|exists:categories,id,user_id,' . $userId,
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|in:pending,in_progress,done',
                'priority' => 'sometimes|in:low,medium,high',
                'due_date' => 'nullable|date|after_or_equal:today'
            ]);

            $task->update($request->all());
            $task->load('category');

            return response()->json([
                'success' => true,
                'data' => new TaskResource($task),
                'message' => 'Tarefa atualizada com sucesso'
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
        $task = Task::where('user_id', $userId)->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarefa excluída com sucesso'
        ]);
    }

    /**
     * Atualizar apenas o status da tarefa
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $userId = auth()->guard('api')->user()->id;
        $task = Task::where('user_id', $userId)->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada'
            ], 404);
        }

        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,done'
            ]);

            $task->update(['status' => $request->status]);
            $task->load('category');

            return response()->json([
                'success' => true,
                'data' => new TaskResource($task),
                'message' => 'Status da tarefa atualizado com sucesso'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
