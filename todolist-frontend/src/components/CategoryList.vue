<template>
  <div class="category-list">
    <div class="header">
      <h2>📋 Categorias</h2>
      <button @click="showCreateForm = !showCreateForm" class="btn-primary">
        {{ showCreateForm ? 'Cancelar' : 'Nova Categoria' }}
      </button>
    </div>

    <!-- Formulário de criação -->
    <div v-if="showCreateForm" class="create-form">
      <input
        v-model="newCategoryName"
        type="text"
        placeholder="Nome da categoria"
        @keyup.enter="handleCreateCategory"
        class="input-field"
      />
      <button @click="handleCreateCategory" :disabled="!newCategoryName.trim()" class="btn-success">
        Criar
      </button>
    </div>

    <!-- Loading -->
    <div v-if="categoryStore.loading" class="loading">
      Carregando categorias...
    </div>

    <!-- Error -->
    <div v-if="categoryStore.error" class="error">
      {{ categoryStore.error }}
      <button @click="categoryStore.clearError()" class="btn-link">Fechar</button>
    </div>

    <!-- Lista de categorias -->
    <div v-if="!categoryStore.loading" class="categories-grid">
      <div
        v-for="category in categoryStore.categories"
        :key="category.id"
        class="category-card"
        @click="$emit('selectCategory', category)"
      >
        <div class="category-info">
          <h3>{{ category.name }}</h3>
          <p>{{ category.tasks_count || 0 }} tarefa(s)</p>
        </div>
        <div class="category-actions">
          <button @click.stop="editCategory(category)" class="btn-edit">✏️</button>
          <button @click.stop="deleteCategory(category)" class="btn-delete">🗑️</button>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="editingCategory" class="modal-overlay" @click="cancelEdit">
      <div class="modal" @click.stop>
        <h3>Editar Categoria</h3>
        <input
          v-model="editCategoryName"
          type="text"
          class="input-field"
          @keyup.enter="handleUpdateCategory"
        />
        <div class="modal-actions">
          <button @click="handleUpdateCategory" class="btn-success">Salvar</button>
          <button @click="cancelEdit" class="btn-secondary">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCategoryStore } from '@/stores/category';
import type { Category } from '@/types/api';

// Props & Emits
defineEmits<{
  selectCategory: [category: Category];
}>();

// Store
const categoryStore = useCategoryStore();

// Estado local
const showCreateForm = ref(false);
const newCategoryName = ref('');
const editingCategory = ref<Category | null>(null);
const editCategoryName = ref('');

// Carregar categorias ao montar o componente
onMounted(() => {
  categoryStore.fetchCategories();
});

// Criar categoria
async function handleCreateCategory() {
  if (!newCategoryName.value.trim()) return;
  
  try {
    await categoryStore.createCategory(newCategoryName.value.trim());
    newCategoryName.value = '';
    showCreateForm.value = false;
  } catch (error) {
    console.error('Erro ao criar categoria:', error);
  }
}

// Editar categoria
function editCategory(category: Category) {
  editingCategory.value = category;
  editCategoryName.value = category.name;
}

async function handleUpdateCategory() {
  if (!editingCategory.value || !editCategoryName.value.trim()) return;
  
  try {
    await categoryStore.updateCategory(editingCategory.value.id, editCategoryName.value.trim());
    cancelEdit();
  } catch (error) {
    console.error('Erro ao atualizar categoria:', error);
  }
}

function cancelEdit() {
  editingCategory.value = null;
  editCategoryName.value = '';
}

// Deletar categoria
async function deleteCategory(category: Category) {
  if (!confirm(`Tem certeza que deseja excluir a categoria "${category.name}"?`)) return;
  
  try {
    await categoryStore.deleteCategory(category.id);
  } catch (error) {
    console.error('Erro ao deletar categoria:', error);
  }
}
</script>

<style scoped>
.category-list {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h2 {
  margin: 0;
  color: #2c3e50;
}

.create-form {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.category-card {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 15px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.category-card:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.category-info h3 {
  margin: 0 0 5px 0;
  color: #2c3e50;
}

.category-info p {
  margin: 0;
  color: #6c757d;
  font-size: 14px;
}

.category-actions {
  display: flex;
  gap: 5px;
}

.loading, .error {
  text-align: center;
  padding: 20px;
  margin: 20px 0;
}

.error {
  background: #f8d7da;
  color: #721c24;
  border-radius: 8px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 20px;
  border-radius: 8px;
  min-width: 300px;
}

.modal-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

/* Botões */
.btn-primary {
  background: #007bff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-success {
  background: #28a745;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-edit, .btn-delete {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
}

.btn-edit:hover {
  background: #e9ecef;
}

.btn-delete:hover {
  background: #f8d7da;
}

.btn-link {
  background: none;
  border: none;
  color: #007bff;
  cursor: pointer;
  text-decoration: underline;
}

.input-field {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
}

.input-field:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
