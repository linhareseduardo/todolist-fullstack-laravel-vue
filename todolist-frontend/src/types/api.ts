// Tipos para a API
export interface Category {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
  tasks_count?: number;
  tasks?: Task[];
}

export interface Task {
  id: number;
  category_id: number;
  title: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'completed';
  priority: 'low' | 'medium' | 'high';
  due_date?: string | null;
  created_at: string;
  updated_at: string;
  category?: Category;
}

// Tipos para formulários
export interface CreateCategoryRequest {
  name: string;
}

export interface CreateTaskRequest {
  category_id: number;
  title: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'completed';
  priority: 'low' | 'medium' | 'high';
  due_date?: string;
}

export interface UpdateTaskStatusRequest {
  status: 'pending' | 'in_progress' | 'completed';
}

// Tipos para respostas da API
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
}

export interface ApiError {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
}
