<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Category;
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
        $query = Task::with('category');

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

        // Ordenação
        $query->orderBy('created_at', 'desc');

        $tasks = $query->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Tarefas listadas com sucesso'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|in:pending,in_progress,done',
                'priority' => 'nullable|in:low,medium,high',
                'due_date' => 'nullable|date|after_or_equal:today'
            ]);

            $task = Task::create($request->all());
            $task->load('category');

            return response()->json([
                'success' => true,
                'data' => $task,
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
        $task = Task::with('category')->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'Tarefa encontrada'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada'
            ], 404);
        }

        try {
            $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
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
                'data' => $task,
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
        $task = Task::find($id);

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
        $task = Task::find($id);

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
                'data' => $task,
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
