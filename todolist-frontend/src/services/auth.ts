import axios from 'axios'
import type { 
  LoginCredentials, 
  RegisterCredentials, 
  AuthResponse, 
  MeResponse, 
  RefreshResponse 
} from '@/types/auth'

const API_BASE_URL = 'http://localhost:8000/api'

// Configurar axios para incluir o token automaticamente
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor para adicionar token automaticamente
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Interceptor para lidar com respostas de erro
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expirado ou inválido
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const authService = {
  // Registrar novo usuário
  async register(credentials: RegisterCredentials): Promise<AuthResponse> {
    const response = await api.post('/auth/register', credentials)
    return response.data
  },

  // Fazer login
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post('/auth/login', credentials)
    return response.data
  },

  // Fazer logout
  async logout(): Promise<void> {
    await api.post('/auth/logout')
  },

  // Obter informações do usuário logado
  async me(): Promise<MeResponse> {
    const response = await api.get('/auth/me')
    return response.data
  },

  // Renovar token
  async refresh(): Promise<RefreshResponse> {
    const response = await api.post('/auth/refresh')
    return response.data
  }
}

export default api
