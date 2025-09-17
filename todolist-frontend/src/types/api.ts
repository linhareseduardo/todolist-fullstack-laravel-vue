// Tipos para a API

// Tipo para data formatada da API
export interface FormattedDate {
  formatted: string;
  iso: string;
  timestamp: number;
  relative: string;
}

export interface Category {
  id: number;
  name: string;
  created_at: FormattedDate;
  updated_at: FormattedDate;
  tasks_count?: number;
  tasks?: Task[];
}

export interface Task {
  id: number;
  category_id: number;
  title: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'done';
  priority: 'low' | 'medium' | 'high';
  due_date?: FormattedDate | null;
  created_at: FormattedDate;
  updated_at: FormattedDate;
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
  status: 'pending' | 'in_progress' | 'done';
  priority: 'low' | 'medium' | 'high';
  due_date?: string;
}

export interface UpdateTaskStatusRequest {
  status: 'pending' | 'in_progress' | 'done';
}

// Tipos para paginação
export interface PaginationInfo {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
  from: number | null;
  to: number | null;
  has_more_pages: boolean;
  prev_page_url: string | null;
  next_page_url: string | null;
}

// Tipos para respostas da API
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
  pagination?: PaginationInfo;
}

export interface ApiError {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
}
