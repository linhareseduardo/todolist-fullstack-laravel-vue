export interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string | null
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterCredentials {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  success: boolean
  message: string
  data: {
    user: User
    token: string
    token_type: string
    expires_in: number
  }
}

export interface AuthErrorResponse {
  success: boolean
  message: string
  errors?: Record<string, string[]>
}

export interface MeResponse {
  success: boolean
  data: User
  message?: string
}

export interface RefreshResponse {
  success: boolean
  data: {
    user: User
    access_token: string
    token_type: string
    expires_in: number
  }
  message: string
}
