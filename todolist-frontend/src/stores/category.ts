import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Category, PaginationInfo } from '@/types/api';
import { categoryService } from '@/services/api';

export const useCategoryStore = defineStore('category', () => {
  // Estado
  const categories = ref<Category[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const pagination = ref<PaginationInfo | null>(null);
  const currentPage = ref(1);

  // Getters
  const categoriesCount = computed(() => categories.value.length);
  const categoriesWithTasks = computed(() =>
    categories.value.filter(cat => (cat.tasks_count || 0) > 0)
  );

  // Actions
  async function fetchCategories(page: number = 1, filters: Record<string, string | number> = {}, append: boolean = false) {
    try {
      loading.value = true;
      error.value = null;

      const response = await categoryService.getAllPaginated(page, filters);

      if (append && page > 1) {
        // Usado apenas para "carregar mais"
        categories.value.push(...response.data);
      } else {
        // Usado para navegação normal entre páginas
        categories.value = response.data;
      }

      pagination.value = response.pagination || null;
      currentPage.value = page;
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Erro ao carregar categorias';
      error.value = errorMessage;
      console.error('Erro ao buscar categorias:', err);
    } finally {
      loading.value = false;
    }
  }

  async function loadMoreCategories(filters: Record<string, string | number> = {}) {
    if (pagination.value && pagination.value.has_more_pages) {
      await fetchCategories(currentPage.value + 1, filters, true);
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

  function clearData() {
    categories.value = [];
    pagination.value = null;
    currentPage.value = 1;
    error.value = null;
  }

  return {
    // Estado
    categories,
    loading,
    error,
    pagination,
    currentPage,
    // Getters
    categoriesCount,
    categoriesWithTasks,
    // Actions
    fetchCategories,
    loadMoreCategories,
    createCategory,
    updateCategory,
    deleteCategory,
    clearError,
    clearData,
  };
});
