import axios from 'axios';
import type { 
  Category, 
  Task, 
  CreateCategoryRequest, 
  CreateTaskRequest, 
  UpdateTaskStatusRequest,
  ApiResponse 
} from '@/types/api';

// Configuração base do Axios
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor para log de requisições (desenvolvimento)
api.interceptors.request.use(
  (config) => {
    console.log(`🚀 ${config.method?.toUpperCase()} ${config.url}`);
    return config;
  },
  (error) => {
    console.error('❌ Request error:', error);
    return Promise.reject(error);
  }
);

// Interceptor para log de respostas (desenvolvimento)
api.interceptors.response.use(
  (response) => {
    console.log(`✅ ${response.status} ${response.config.url}`);
    return response;
  },
  (error) => {
    console.error('❌ Response error:', error.response?.data || error.message);
    return Promise.reject(error);
  }
);

// Serviços de Categories
export const categoryService = {
  // Listar todas as categorias
  async getAll(): Promise<Category[]> {
    const response = await api.get<ApiResponse<Category[]>>('/categories');
    return response.data.data;
  },

  // Buscar categoria por ID
  async getById(id: number): Promise<Category> {
    const response = await api.get<ApiResponse<Category>>(`/categories/${id}`);
    return response.data.data;
  },

  // Criar nova categoria
  async create(data: CreateCategoryRequest): Promise<Category> {
    const response = await api.post<ApiResponse<Category>>('/categories', data);
    return response.data.data;
  },

  // Atualizar categoria
  async update(id: number, data: CreateCategoryRequest): Promise<Category> {
    const response = await api.put<ApiResponse<Category>>(`/categories/${id}`, data);
    return response.data.data;
  },

  // Deletar categoria
  async delete(id: number): Promise<void> {
    await api.delete(`/categories/${id}`);
  },
};

// Serviços de Tasks
export const taskService = {
  // Listar todas as tarefas
  async getAll(filters?: {
    status?: string;
    priority?: string;
    category_id?: number;
  }): Promise<Task[]> {
    const response = await api.get<ApiResponse<Task[]>>('/tasks', {
      params: filters,
    });
    return response.data.data;
  },

  // Buscar tarefa por ID
  async getById(id: number): Promise<Task> {
    const response = await api.get<ApiResponse<Task>>(`/tasks/${id}`);
    return response.data.data;
  },

  // Criar nova tarefa
  async create(data: CreateTaskRequest): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>('/tasks', data);
    return response.data.data;
  },

  // Atualizar tarefa
  async update(id: number, data: CreateTaskRequest): Promise<Task> {
    const response = await api.put<ApiResponse<Task>>(`/tasks/${id}`, data);
    return response.data.data;
  },

  // Atualizar apenas o status da tarefa
  async updateStatus(id: number, data: UpdateTaskStatusRequest): Promise<Task> {
    const response = await api.patch<ApiResponse<Task>>(`/tasks/${id}/status`, data);
    return response.data.data;
  },

  // Deletar tarefa
  async delete(id: number): Promise<void> {
    await api.delete(`/tasks/${id}`);
  },
};

export default api;
