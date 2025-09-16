import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Category } from '@/types/api';
import { categoryService } from '@/services/api';

export const useCategoryStore = defineStore('category', () => {
  // Estado
  const categories = ref<Category[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Getters
  const categoriesCount = computed(() => categories.value.length);
  const categoriesWithTasks = computed(() => 
    categories.value.filter(cat => (cat.tasks_count || 0) > 0)
  );

  // Actions
  async function fetchCategories() {
    try {
      loading.value = true;
      error.value = null;
      categories.value = await categoryService.getAll();
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao carregar categorias';
      error.value = errorMessage;
      console.error('Erro ao buscar categorias:', err);
    } finally {
      loading.value = false;
    }
  }

  async function createCategory(name: string) {
    try {
      loading.value = true;
      error.value = null;
      const newCategory = await categoryService.create({ name });
      categories.value.push(newCategory);
      return newCategory;
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao criar categoria';
      error.value = errorMessage;
      console.error('Erro ao criar categoria:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateCategory(id: number, name: string) {
    try {
      loading.value = true;
      error.value = null;
      const updatedCategory = await categoryService.update(id, { name });
      const index = categories.value.findIndex(cat => cat.id === id);
      if (index !== -1) {
        categories.value[index] = updatedCategory;
      }
      return updatedCategory;
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao atualizar categoria';
      error.value = errorMessage;
      console.error('Erro ao atualizar categoria:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteCategory(id: number) {
    try {
      loading.value = true;
      error.value = null;
      await categoryService.delete(id);
      categories.value = categories.value.filter(cat => cat.id !== id);
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao deletar categoria';
      error.value = errorMessage;
      console.error('Erro ao deletar categoria:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function clearError() {
    error.value = null;
  }

  return {
    // Estado
    categories,
    loading,
    error,
    // Getters
    categoriesCount,
    categoriesWithTasks,
    // Actions
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory,
    clearError,
  };
});
