import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, LoginCredentials, RegisterCredentials } from '@/types/auth'
import { authService } from '@/services/auth'

export const useAuthStore = defineStore('auth', () => {
  // Estado
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userName = computed(() => user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')

  // Actions
  const setToken = (newToken: string) => {
    token.value = newToken
    localStorage.setItem('auth_token', newToken)
  }

  const removeToken = () => {
    token.value = null
    localStorage.removeItem('auth_token')
  }

  const setUser = (userData: User) => {
    user.value = userData
  }

  const clearUser = () => {
    user.value = null
  }

  const setError = (message: string) => {
    error.value = message
    setTimeout(() => {
      error.value = null
    }, 5000)
  }

  const clearError = () => {
    error.value = null
  }

  // Registro de usuário
  const register = async (credentials: RegisterCredentials) => {
    loading.value = true
    clearError()

    try {
      const response = await authService.register(credentials)
      
      if (response.success) {
        setToken(response.data.token)
        setUser(response.data.user)
        return { success: true }
      } else {
        setError(response.message || 'Erro ao registrar usuário')
        return { success: false, message: response.message }
      }
    } catch (err: unknown) {
      const error = err as { response?: { data?: { message?: string } } }
      const message = error.response?.data?.message || 'Erro ao registrar usuário'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Login
  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    clearError()

    try {
      const response = await authService.login(credentials)
      
      if (response.success) {
        setToken(response.data.token)
        setUser(response.data.user)
        return { success: true }
      } else {
        setError(response.message || 'Credenciais inválidas')
        return { success: false, message: response.message }
      }
    } catch (err: unknown) {
      const error = err as { response?: { data?: { message?: string } } }
      const message = error.response?.data?.message || 'Erro ao fazer login'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Logout
  const logout = async () => {
    loading.value = true

    try {
      if (token.value) {
        await authService.logout()
      }
    } catch (err) {
      console.error('Erro ao fazer logout:', err)
    } finally {
      removeToken()
      clearUser()
      loading.value = false
    }
  }

  // Obter informações do usuário logado
  const getMe = async () => {
    if (!token.value) return { success: false }

    loading.value = true

    try {
      const response = await authService.me()
      
      if (response.success) {
        setUser(response.data)
        return { success: true }
      } else {
        // Token pode estar inválido
        await logout()
        return { success: false }
      }
    } catch (err) {
      console.error('Erro ao obter dados do usuário:', err)
      await logout()
      return { success: false }
    } finally {
      loading.value = false
    }
  }

  // Verificar autenticação ao iniciar a aplicação
  const checkAuth = async () => {
    if (token.value && !user.value) {
      await getMe()
    }
  }

  // Refresh token
  const refreshToken = async () => {
    if (!token.value) return { success: false }

    try {
      const response = await authService.refresh()
      
      if (response.success) {
        setToken(response.data.access_token)
        return { success: true }
      } else {
        await logout()
        return { success: false }
      }
    } catch (err) {
      console.error('Erro ao renovar token:', err)
      await logout()
      return { success: false }
    }
  }

  return {
    // Estado
    user,
    token,
    loading,
    error,
    
    // Getters
    isAuthenticated,
    userName,
    userEmail,
    
    // Actions
    register,
    login,
    logout,
    getMe,
    checkAuth,
    refreshToken,
    clearError,
    setError
  }
})
