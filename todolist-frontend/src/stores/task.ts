import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Task } from '@/types/api';
import { taskService } from '@/services/api';

export const useTaskStore = defineStore('task', () => {
  // Estado
  const tasks = ref<Task[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Getters
  const tasksCount = computed(() => tasks.value.length);
  const pendingTasks = computed(() => 
    tasks.value.filter(task => task.status === 'pending')
  );
  const inProgressTasks = computed(() => 
    tasks.value.filter(task => task.status === 'in_progress')
  );
  const doneTasks = computed(() => 
    tasks.value.filter(task => task.status === 'done')
  );
  const highPriorityTasks = computed(() => 
    tasks.value.filter(task => task.priority === 'high')
  );

  // Actions
  async function fetchTasks(filters?: {
    status?: string;
    priority?: string;
    category_id?: number;
  }) {
    try {
      loading.value = true;
      error.value = null;
      tasks.value = await taskService.getAll(filters);
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao carregar tarefas';
      error.value = errorMessage;
      console.error('Erro ao buscar tarefas:', err);
    } finally {
      loading.value = false;
    }
  }

  async function createTask(taskData: {
    category_id: number;
    title: string;
    description: string;
    status: 'pending' | 'in_progress' | 'done';
    priority: 'low' | 'medium' | 'high';
    due_date: string;
  }) {
    try {
      loading.value = true;
      error.value = null;
      const newTask = await taskService.create(taskData);
      tasks.value.push(newTask);
      return newTask;
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao criar tarefa';
      error.value = errorMessage;
      console.error('Erro ao criar tarefa:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateTask(id: number, taskData: {
    category_id: number;
    title: string;
    description: string;
    status: 'pending' | 'in_progress' | 'done';
    priority: 'low' | 'medium' | 'high';
    due_date: string;
  }) {
    try {
      loading.value = true;
      error.value = null;
      const updatedTask = await taskService.update(id, taskData);
      const index = tasks.value.findIndex(task => task.id === id);
      if (index !== -1) {
        tasks.value[index] = updatedTask;
      }
      return updatedTask;
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao atualizar tarefa';
      error.value = errorMessage;
      console.error('Erro ao atualizar tarefa:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateTaskStatus(id: number, status: 'pending' | 'in_progress' | 'done') {
    try {
      loading.value = true;
      error.value = null;
      const updatedTask = await taskService.updateStatus(id, { status });
      const index = tasks.value.findIndex(task => task.id === id);
      if (index !== -1) {
        tasks.value[index] = updatedTask;
      }
      return updatedTask;
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao atualizar status';
      error.value = errorMessage;
      console.error('Erro ao atualizar status:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteTask(id: number) {
    try {
      loading.value = true;
      error.value = null;
      await taskService.delete(id);
      tasks.value = tasks.value.filter(task => task.id !== id);
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao deletar tarefa';
      error.value = errorMessage;
      console.error('Erro ao deletar tarefa:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function clearError() {
    error.value = null;
  }

  return {
    // Estado
    tasks,
    loading,
    error,
    // Getters
    tasksCount,
    pendingTasks,
    inProgressTasks,
    doneTasks,
    highPriorityTasks,
    // Actions
    fetchTasks,
    createTask,
    updateTask,
    updateTaskStatus,
    deleteTask,
    clearError,
  };
});
