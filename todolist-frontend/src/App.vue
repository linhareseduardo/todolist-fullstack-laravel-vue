<template>
  <div id="app">
    <!-- Header com informações do usuário (apenas se autenticado) -->
    <header v-if="authStore.isAuthenticated" class="app-header">
      <div class="header-content">
        <h1>TodoList</h1>
        <div class="user-info">
          <span class="user-name">Olá, {{ authStore.userName }}!</span>
          <button @click="handleLogout" class="logout-button">
            Sair
          </button>
        </div>
      </div>
      <nav class="app-nav">
        <button
          @click="activeTab = 'tasks'"
          :class="{ active: activeTab === 'tasks' }"
          class="nav-button"
        >
          📋 Tarefas
        </button>
        <button
          @click="activeTab = 'categories'"
          :class="{ active: activeTab === 'categories' }"
          class="nav-button"
        >
          📁 Categorias
        </button>
      </nav>
    </header>

    <!-- Conteúdo principal -->
    <main class="app-main">
      <!-- Se autenticado, mostra as abas -->
      <div v-if="authStore.isAuthenticated">
        <div v-show="activeTab === 'tasks'" class="tab-content">
          <TaskList />
        </div>
        
        <div v-show="activeTab === 'categories'" class="tab-content">
          <CategoryList />
        </div>
      </div>
      
      <!-- Se não autenticado, mostra as rotas de auth -->
      <div v-else>
        <RouterView />
      </div>
    </main>

    <!-- Footer (apenas se autenticado) -->
    <footer v-if="authStore.isAuthenticated" class="app-footer">
      <p>&copy; 2025 TodoList App - Laravel API + Vue.js + TypeScript</p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import CategoryList from './components/CategoryList.vue'
import TaskList from './components/TaskList.vue'

const router = useRouter()
const authStore = useAuthStore()
const activeTab = ref('tasks')

// Verificar autenticação ao carregar a aplicação
onMounted(async () => {
  await authStore.checkAuth()
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f5f7fa;
  color: #333;
  line-height: 1.6;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header */
.app-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.header-content h1 {
  font-size: 1.8rem;
  font-weight: bold;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  font-weight: 500;
  font-size: 1rem;
}

.logout-button {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 6px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.logout-button:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-1px);
}

/* Navegação */
.app-nav {
  display: flex;
  gap: 1rem;
}

.nav-button {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav-button:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

.nav-button.active {
  background: rgba(255, 255, 255, 0.25);
  border-color: rgba(255, 255, 255, 0.4);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Conteúdo principal */
.app-main {
  flex: 1;
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.tab-content {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  min-height: 600px;
}

/* Footer */
.app-footer {
  background: #f8f9fa;
  text-align: center;
  padding: 1rem;
  border-top: 1px solid #e9ecef;
  color: #6c757d;
  font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
  .app-header {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .app-nav {
    flex-direction: column;
    width: 100%;
  }
  
  .nav-button {
    justify-content: center;
  }
  
  .app-main {
    padding: 1rem;
  }
  
  .tab-content {
    padding: 1rem;
  }
}

/* Animações */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.tab-content {
  animation: fadeIn 0.3s ease-out;
}
</style>
