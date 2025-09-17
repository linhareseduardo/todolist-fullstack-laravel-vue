import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Task, CreateTaskRequest, ApiResponse, PaginationInfo } from '../types/api'
import api from '../services/api'

export const useTaskStore = defineStore('task', () => {
  const tasks = ref<Task[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<PaginationInfo | null>(null)
  const currentPage = ref(1)

  const fetchTasks = async (page: number = 1, filters?: Record<string, string | number>, append: boolean = false): Promise<void> => {
    loading.value = true
    error.value = null
    try {
      const params = new URLSearchParams()
      params.append('page', page.toString())

      if (filters) {
        Object.entries(filters).forEach(([key, value]) => {
          if (value) {
            params.append(key, value.toString())
          }
        })
      }

      const response = await api.get<ApiResponse<Task[]>>(`/tasks?${params.toString()}`)
      if (response.data.success) {
        if (append && page > 1) {
          // Usado apenas para "carregar mais"
          tasks.value.push(...response.data.data)
        } else {
          // Usado para navegação normal entre páginas
          tasks.value = response.data.data
        }
        pagination.value = response.data.pagination || null
        currentPage.value = page
      } else {
        throw new Error(response.data.message || 'Erro ao carregar tarefas')
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar tarefas:', err)
    } finally {
      loading.value = false
    }
  }

  const loadMoreTasks = async (filters?: Record<string, string | number>): Promise<void> => {
    if (pagination.value && pagination.value.has_more_pages) {
      await fetchTasks(currentPage.value + 1, filters, true)
    }
  }

  const createTask = async (taskData: CreateTaskRequest): Promise<Task | null> => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post<ApiResponse<Task>>('/tasks', taskData)
      if (response.data.success) {
        const newTask = response.data.data
        tasks.value.push(newTask)
        return newTask
      } else {
        throw new Error(response.data.message || 'Erro ao criar tarefa')
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao criar tarefa:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateTask = async (id: number, taskData: Partial<CreateTaskRequest>): Promise<Task | null> => {
    loading.value = true
    error.value = null
    try {
      const response = await api.put<ApiResponse<Task>>(`/tasks/${id}`, taskData)
      if (response.data.success) {
        const updatedTask = response.data.data
        const index = tasks.value.findIndex(task => task.id === id)
        if (index !== -1) {
          tasks.value[index] = updatedTask
        }
        return updatedTask
      } else {
        throw new Error(response.data.message || 'Erro ao atualizar tarefa')
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao atualizar tarefa:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteTask = async (id: number): Promise<void> => {
    loading.value = true
    error.value = null
    try {
      const response = await api.delete<ApiResponse<null>>(`/tasks/${id}`)
      if (response.data.success) {
        tasks.value = tasks.value.filter(task => task.id !== id)
      } else {
        throw new Error(response.data.message || 'Erro ao excluir tarefa')
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao excluir tarefa:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const getTaskById = (id: number): Task | undefined => {
    return tasks.value.find(task => task.id === id)
  }

  const getTasksByCategory = (categoryId: number): Task[] => {
    return tasks.value.filter(task => task.category_id === categoryId)
  }

  const getTasksByStatus = (status: string): Task[] => {
    return tasks.value.filter(task => task.status === status)
  }

  const getTasksByPriority = (priority: string): Task[] => {
    return tasks.value.filter(task => task.priority === priority)
  }

  const clearError = (): void => {
    error.value = null
  }

  return {
    tasks,
    loading,
    error,
    pagination,
    currentPage,
    fetchTasks,
    loadMoreTasks,
    createTask,
    updateTask,
    deleteTask,
    getTaskById,
    getTasksByCategory,
    getTasksByStatus,
    getTasksByPriority,
    clearError
  }
})
