<template>
  <div id="app">
    <header class="app-header">
      <h1>TodoList - Gerenciador de Tarefas</h1>
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

    <main class="app-main">
      <div v-show="activeTab === 'tasks'" class="tab-content">
        <TaskList />
      </div>
      
      <div v-show="activeTab === 'categories'" class="tab-content">
        <CategoryList />
      </div>
    </main>

    <footer class="app-footer">
      <p>&copy; 2025 TodoList App - Laravel API + Vue.js + TypeScript</p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import CategoryList from './components/CategoryList.vue'
import TaskList from './components/TaskList.vue'

const activeTab = ref('tasks')
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background-color: #f5f7fa;
  color: #333;
  line-height: 1.6;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.app-header h1 {
  text-align: center;
  margin-bottom: 20px;
  font-size: 2rem;
  font-weight: 600;
}

.app-nav {
  display: flex;
  justify-content: center;
  gap: 20px;
}

.nav-button {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  padding: 12px 24px;
  border-radius: 25px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.nav-button:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
}

.nav-button.active {
  background: white;
  color: #667eea;
  border-color: white;
}

.app-main {
  flex: 1;
  padding: 0;
  max-width: 100%;
  margin: 0 auto;
}

.tab-content {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.app-footer {
  background: #2c3e50;
  color: white;
  text-align: center;
  padding: 20px;
  margin-top: auto;
}

.app-footer p {
  font-size: 14px;
  opacity: 0.8;
}

@media (max-width: 768px) {
  .app-header {
    padding: 15px;
  }
  
  .app-header h1 {
    font-size: 1.5rem;
    margin-bottom: 15px;
  }
  
  .app-nav {
    flex-direction: column;
    gap: 10px;
  }
  
  .nav-button {
    padding: 10px 20px;
    font-size: 14px;
  }
}
</style>
