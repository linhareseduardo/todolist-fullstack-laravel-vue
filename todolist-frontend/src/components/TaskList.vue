<template>
  <div class="task-list">
    <div class="header">
      <h2>Lista de Tarefas</h2>
      <div class="filters">
        <select v-model="selectedCategory" @change="filterTasks" class="filter-select">
          <option value="">Todas as categorias</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <select v-model="selectedStatus" @change="filterTasks" class="filter-select">
          <option value="">Todos os status</option>
          <option value="pending">Pendente</option>
          <option value="in_progress">Em Progresso</option>
          <option value="completed">Concluída</option>
        </select>
        <select v-model="selectedPriority" @change="filterTasks" class="filter-select">
          <option value="">Todas as prioridades</option>
          <option value="low">Baixa</option>
          <option value="medium">Média</option>
          <option value="high">Alta</option>
        </select>
      </div>
      <button @click="showCreateForm = true" class="btn-primary">Nova Tarefa</button>
    </div>

    <!-- Formulário de criação/edição de tarefa -->
    <div v-if="showCreateForm || editingTask" class="form-container">
      <h3>{{ editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}</h3>
      <form @submit.prevent="saveTask" class="task-form">
        <div class="form-group">
          <label for="title">Título:</label>
          <input
            type="text"
            id="title"
            v-model="taskForm.title"
            required
            maxlength="255"
            class="form-input"
          />
        </div>
        
        <div class="form-group">
          <label for="description">Descrição:</label>
          <textarea
            id="description"
            v-model="taskForm.description"
            rows="4"
            class="form-textarea"
          ></textarea>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="category">Categoria:</label>
            <select id="category" v-model="taskForm.category_id" required class="form-select">
              <option value="">Selecione uma categoria</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="priority">Prioridade:</label>
            <select id="priority" v-model="taskForm.priority" required class="form-select">
              <option value="low">Baixa</option>
              <option value="medium">Média</option>
              <option value="high">Alta</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" v-model="taskForm.status" required class="form-select">
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Progresso</option>
              <option value="completed">Concluída</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="due_date">Data de Vencimento:</label>
          <input
            type="date"
            id="due_date"
            v-model="taskForm.due_date"
            class="form-input"
          />
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Salvando...' : (editingTask ? 'Atualizar' : 'Criar') }}
          </button>
          <button type="button" @click="cancelForm" class="btn-secondary">Cancelar</button>
        </div>
      </form>
    </div>

    <!-- Lista de tarefas -->
    <div class="tasks-grid">
      <div v-if="loading && !filteredTasks.length" class="loading">
        Carregando tarefas...
      </div>
      
      <div v-else-if="!filteredTasks.length" class="empty-state">
        <p>Nenhuma tarefa encontrada.</p>
      </div>
      
      <div v-else class="task-cards">
        <div
          v-for="task in filteredTasks"
          :key="task.id"
          class="task-card"
          :class="[
            `priority-${task.priority}`,
            `status-${task.status}`,
            { 'overdue': isOverdue(task.due_date) }
          ]"
        >
          <div class="task-header">
            <h3 class="task-title">{{ task.title }}</h3>
            <div class="task-actions">
              <button @click="editTask(task)" class="btn-edit" title="Editar">✏️</button>
              <button @click="deleteTask(task.id)" class="btn-delete" title="Excluir">🗑️</button>
            </div>
          </div>
          
          <p v-if="task.description" class="task-description">{{ task.description }}</p>
          
          <div class="task-meta">
            <span class="task-category">{{ getCategoryName(task.category_id) }}</span>
            <span class="task-priority" :class="`priority-${task.priority}`">
              {{ getPriorityLabel(task.priority) }}
            </span>
            <span class="task-status" :class="`status-${task.status}`">
              {{ getStatusLabel(task.status) }}
            </span>
          </div>
          
          <div v-if="task.due_date" class="task-due-date" :class="{ 'overdue': isOverdue(task.due_date) }">
            <strong>Vence:</strong> {{ formatDueDate(task.due_date) }}
          </div>
          
          <div class="task-dates">
            <small>Criada: {{ formatDate(task.created_at) }}</small>
            <small v-if="task.updated_at !== task.created_at">
              Atualizada: {{ formatDate(task.updated_at) }}
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCategoryStore } from '../stores/category'
import { useTaskStore } from '../stores/taskStore'
import type { Task, CreateTaskRequest, Category, FormattedDate } from '../types/api'

const categoryStore = useCategoryStore()
const taskStore = useTaskStore()

// Estado reativo
const showCreateForm = ref(false)
const editingTask = ref<Task | null>(null)
const loading = ref(false)
const selectedCategory = ref('')
const selectedStatus = ref('')
const selectedPriority = ref('')

// Formulário de tarefa
const taskForm = ref<CreateTaskRequest>({
  title: '',
  description: '',
  category_id: 0,
  priority: 'medium',
  status: 'pending',
  due_date: ''
})

// Computed properties
const categories = computed(() => categoryStore.categories)
const tasks = computed(() => taskStore.tasks)

const filteredTasks = computed(() => {
  let filtered = tasks.value

  if (selectedCategory.value) {
    filtered = filtered.filter((task: Task) => task.category_id === parseInt(selectedCategory.value))
  }

  if (selectedStatus.value) {
    filtered = filtered.filter((task: Task) => task.status === selectedStatus.value)
  }

  if (selectedPriority.value) {
    filtered = filtered.filter((task: Task) => task.priority === selectedPriority.value)
  }

  return filtered
})

// Métodos
const loadData = async () => {
  loading.value = true
  try {
    await Promise.all([
      categoryStore.fetchCategories(),
      taskStore.fetchTasks()
    ])
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
  } finally {
    loading.value = false
  }
}

const filterTasks = () => {
  // Os filtros são aplicados automaticamente através do computed property
}

const saveTask = async () => {
  loading.value = true
  try {
    if (editingTask.value) {
      await taskStore.updateTask(editingTask.value.id, taskForm.value)
    } else {
      await taskStore.createTask(taskForm.value)
    }
    cancelForm()
  } catch (error) {
    console.error('Erro ao salvar tarefa:', error)
  } finally {
    loading.value = false
  }
}

const editTask = (task: Task) => {
  editingTask.value = task
  taskForm.value = {
    title: task.title,
    description: task.description || '',
    category_id: task.category_id,
    priority: task.priority,
    status: task.status,
    due_date: task.due_date ? formatDateForInput(task.due_date) : ''
  }
  showCreateForm.value = false
}

const deleteTask = async (id: number) => {
  if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
    try {
      await taskStore.deleteTask(id)
    } catch (error) {
      console.error('Erro ao excluir tarefa:', error)
    }
  }
}

const cancelForm = () => {
  showCreateForm.value = false
  editingTask.value = null
  taskForm.value = {
    title: '',
    description: '',
    category_id: 0,
    priority: 'medium',
    status: 'pending',
    due_date: ''
  }
}

const getCategoryName = (categoryId: number): string => {
  const category = categories.value.find((cat: Category) => cat.id === categoryId)
  return category ? category.name : 'Categoria não encontrada'
}

const getPriorityLabel = (priority: string): string => {
  const labels: Record<string, string> = {
    low: 'Baixa',
    medium: 'Média',
    high: 'Alta'
  }
  return labels[priority] || priority
}

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    pending: 'Pendente',
    in_progress: 'Em Progresso',
    completed: 'Concluída'
  }
  return labels[status] || status
}

const formatDate = (dateObject: string | FormattedDate | null): string => {
  if (!dateObject) return ''
  
  // Se for um objeto com formato personalizado (da nossa API)
  if (typeof dateObject === 'object' && dateObject.formatted) {
    return dateObject.formatted
  }
  
  // Se for uma string (formato antigo)
  if (typeof dateObject === 'string') {
    const date = new Date(dateObject)
    return date.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      timeZone: 'America/Sao_Paulo'
    })
  }
  
  return ''
}

const formatDueDate = (dateObject: string | FormattedDate | null): string => {
  if (!dateObject) return ''
  
  // Se for um objeto com formato personalizado (da nossa API)
  if (typeof dateObject === 'object' && dateObject.formatted) {
    return dateObject.formatted
  }
  
  // Se for uma string (formato antigo)
  if (typeof dateObject === 'string') {
    const date = new Date(dateObject)
    return date.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      timeZone: 'America/Sao_Paulo'
    })
  }
  
  return ''
}

const formatDateForInput = (dateObject: string | FormattedDate | null): string => {
  if (!dateObject) return ''
  
  let dateString = ''
  
  // Se for um objeto com formato personalizado (da nossa API)
  if (typeof dateObject === 'object' && dateObject.iso) {
    dateString = dateObject.iso
  } else if (typeof dateObject === 'string') {
    dateString = dateObject
  } else {
    return ''
  }
  
  // Para input date, precisamos do formato: YYYY-MM-DD
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  
  return `${year}-${month}-${day}`
}

const isOverdue = (dueDate: string | null): boolean => {
  if (!dueDate) return false
  return new Date(dueDate) < new Date()
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.task-list {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 15px;
}

.header h2 {
  margin: 0;
  color: #333;
}

.filters {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
  font-size: 14px;
}

.btn-primary {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-primary:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
}

.btn-secondary:hover {
  background: #545b62;
}

.form-container {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.form-container h3 {
  margin: 0 0 20px 0;
  color: #333;
}

.task-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 5px;
  font-weight: 500;
  color: #333;
}

.form-input,
.form-textarea,
.form-select {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-start;
}

.tasks-grid {
  margin-top: 20px;
}

.loading,
.empty-state {
  text-align: center;
  padding: 40px;
  color: #666;
}

.task-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.task-card {
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s ease;
}

.task-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.task-card.overdue {
  border-left: 4px solid #dc3545;
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.task-title {
  margin: 0;
  color: #333;
  font-size: 18px;
  font-weight: 600;
  flex: 1;
}

.task-actions {
  display: flex;
  gap: 5px;
}

.btn-edit,
.btn-delete {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
  font-size: 16px;
}

.btn-edit:hover {
  background: #e9ecef;
}

.btn-delete:hover {
  background: #f8d7da;
}

.task-description {
  color: #666;
  margin: 10px 0;
  line-height: 1.5;
}

.task-meta {
  display: flex;
  gap: 10px;
  margin: 15px 0;
  flex-wrap: wrap;
}

.task-category,
.task-priority,
.task-status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.task-category {
  background: #e9ecef;
  color: #495057;
}

.priority-low {
  background: #d4edda;
  color: #155724;
}

.priority-medium {
  background: #fff3cd;
  color: #856404;
}

.priority-high {
  background: #f8d7da;
  color: #721c24;
}

.status-pending {
  background: #d1ecf1;
  color: #0c5460;
}

.status-in_progress {
  background: #fff3cd;
  color: #856404;
}

.status-completed {
  background: #d4edda;
  color: #155724;
}

.task-due-date {
  margin: 10px 0;
  padding: 8px;
  background: #f8f9fa;
  border-radius: 4px;
  font-size: 14px;
}

.task-due-date.overdue {
  background: #f8d7da;
  color: #721c24;
}

.task-dates {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
}

.task-dates small {
  color: #6c757d;
  font-size: 12px;
}

@media (max-width: 768px) {
  .task-list {
    padding: 15px;
  }

  .header {
    flex-direction: column;
    align-items: stretch;
  }

  .filters {
    justify-content: center;
  }

  .task-cards {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .task-dates {
    flex-direction: column;
    gap: 5px;
  }
}
</style>
