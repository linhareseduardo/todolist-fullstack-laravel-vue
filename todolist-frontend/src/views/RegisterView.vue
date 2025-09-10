<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>TodoList</h1>
        <p>Crie sua conta</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label for="name">Nome</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            placeholder="Seu nome completo"
            :disabled="authStore.loading"
          />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            placeholder="seu@email.com"
            :disabled="authStore.loading"
          />
        </div>

        <div class="form-group">
          <label for="password">Senha</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            placeholder="Mínimo 8 caracteres"
            :disabled="authStore.loading"
            minlength="8"
          />
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirmar Senha</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            placeholder="Digite a senha novamente"
            :disabled="authStore.loading"
          />
        </div>

        <button 
          type="submit" 
          class="register-button"
          :disabled="authStore.loading || !isFormValid"
        >
          <span v-if="authStore.loading">Criando conta...</span>
          <span v-else>Criar Conta</span>
        </button>

        <div v-if="authStore.error" class="error-message">
          {{ authStore.error }}
        </div>
      </form>

      <div class="register-footer">
        <p>
          Já tem uma conta? 
          <router-link to="/login" class="login-link">
            Entre aqui
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const isFormValid = computed(() => {
  return form.value.name.trim() !== '' &&
         form.value.email.trim() !== '' &&
         form.value.password.length >= 8 &&
         form.value.password === form.value.password_confirmation
})

const handleRegister = async () => {
  if (!isFormValid.value) return
  
  const result = await authStore.register(form.value)
  
  if (result.success) {
    router.push('/')
  }
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.register-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  padding: 40px;
  width: 100%;
  max-width: 400px;
}

.register-header {
  text-align: center;
  margin-bottom: 30px;
}

.register-header h1 {
  color: #333;
  margin: 0 0 10px 0;
  font-size: 2rem;
  font-weight: bold;
}

.register-header p {
  color: #666;
  margin: 0;
  font-size: 1rem;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
  color: #333;
  font-size: 0.9rem;
}

.form-group input {
  padding: 12px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.register-button {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 6px;
  padding: 12px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.register-button:hover:not(:disabled) {
  transform: translateY(-2px);
}

.register-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.error-message {
  background-color: #fee;
  color: #c33;
  padding: 12px;
  border-radius: 6px;
  border: 1px solid #fcc;
  text-align: center;
  font-size: 0.9rem;
}

.register-footer {
  text-align: center;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.register-footer p {
  color: #666;
  margin: 0;
}

.login-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.login-link:hover {
  text-decoration: underline;
}
</style>
