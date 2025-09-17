<template>
  <div v-if="pagination && pagination.total > pagination.per_page" class="pagination">
    <div class="pagination-info">
      Mostrando {{ pagination.from || 0 }} a {{ pagination.to || 0 }} de {{ pagination.total }} itens
    </div>

    <!-- Paginação com números -->
    <div class="pagination-numbers">
      <!-- Botão Anterior -->
      <button
        @click="$emit('goToPage', pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        class="pagination-btn nav-btn"
        :class="{ disabled: pagination.current_page === 1 }"
      >
        ‹
      </button>

      <!-- Primeira página -->
      <button
        v-if="showFirstPage"
        @click="$emit('goToPage', 1)"
        class="pagination-btn page-btn"
        :class="{ active: pagination.current_page === 1 }"
      >
        1
      </button>

      <!-- Reticências iniciais -->
      <span v-if="showStartEllipsis" class="pagination-ellipsis">...</span>

      <!-- Páginas do meio -->
      <button
        v-for="page in visiblePages"
        :key="page"
        @click="$emit('goToPage', page)"
        class="pagination-btn page-btn"
        :class="{ active: pagination.current_page === page }"
      >
        {{ page }}
      </button>

      <!-- Reticências finais -->
      <span v-if="showEndEllipsis" class="pagination-ellipsis">...</span>

      <!-- Última página -->
      <button
        v-if="showLastPage"
        @click="$emit('goToPage', pagination.last_page)"
        class="pagination-btn page-btn"
        :class="{ active: pagination.current_page === pagination.last_page }"
      >
        {{ pagination.last_page }}
      </button>

      <!-- Botão Próxima -->
      <button
        @click="$emit('goToPage', pagination.current_page + 1)"
        :disabled="!pagination.has_more_pages"
        class="pagination-btn nav-btn"
        :class="{ disabled: !pagination.has_more_pages }"
      >
        ›
      </button>
    </div>

    <!-- Botão "Carregar mais" alternativo -->
    <div v-if="showLoadMore" class="load-more-section">
      <button
        @click="$emit('loadMore')"
        :disabled="!pagination.has_more_pages || loading"
        class="load-more-btn"
        :class="{ disabled: !pagination.has_more_pages || loading }"
      >
        {{ loading ? 'Carregando...' : 'Carregar mais' }}
      </button>
    </div>
  </div>
</template><script setup lang="ts">
import { computed } from 'vue';
import type { PaginationInfo } from '@/types/api';

interface Props {
  pagination: PaginationInfo | null;
  loading?: boolean;
  showLoadMore?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  showLoadMore: false
});

defineEmits<{
  previousPage: [];
  nextPage: [];
  loadMore: [];
  goToPage: [page: number];
}>();

// Calcular páginas visíveis
const visiblePages = computed(() => {
  if (!props.pagination) return [];

  const current = props.pagination.current_page;
  const last = props.pagination.last_page;
  const pages: number[] = [];

  // Mostrar até 5 páginas ao redor da página atual
  const startPage = Math.max(2, current - 2);
  const endPage = Math.min(last - 1, current + 2);

  for (let i = startPage; i <= endPage; i++) {
    if (i !== 1 && i !== last) {
      pages.push(i);
    }
  }

  return pages;
});

// Verificar se deve mostrar primeira página
const showFirstPage = computed(() => {
  if (!props.pagination) return false;
  return props.pagination.last_page > 1;
});

// Verificar se deve mostrar última página
const showLastPage = computed(() => {
  if (!props.pagination) return false;
  // Sempre mostrar a última página se houver mais de uma página
  // e se ela não estiver já incluída nas páginas visíveis
  const lastPage = props.pagination.last_page;
  const visiblePagesArray = visiblePages.value;
  return lastPage > 1 && !visiblePagesArray.includes(lastPage);
});

// Verificar se deve mostrar reticências iniciais
const showStartEllipsis = computed(() => {
  if (!props.pagination) return false;
  return props.pagination.current_page > 4;
});

// Verificar se deve mostrar reticências finais
const showEndEllipsis = computed(() => {
  if (!props.pagination) return false;
  return props.pagination.current_page < props.pagination.last_page - 3;
});
</script>

<style scoped>
.pagination {
  margin-top: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.pagination-info {
  text-align: center;
  margin-bottom: 15px;
  color: #6c757d;
  font-size: 14px;
}

.pagination-numbers {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 4px;
  margin-bottom: 15px;
}

.pagination-btn {
  border: none;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
  min-width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.nav-btn {
  background: #007bff;
  color: white;
  font-size: 16px;
  font-weight: bold;
}

.nav-btn:hover:not(.disabled) {
  background: #0056b3;
}

.nav-btn.disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}

.page-btn {
  background: white;
  color: #007bff;
  border: 1px solid #dee2e6;
}

.page-btn:hover:not(.active) {
  background: #e9ecef;
  color: #0056b3;
}

.page-btn.active {
  background: #007bff;
  color: white;
  font-weight: bold;
}

.pagination-ellipsis {
  padding: 0 8px;
  color: #6c757d;
  font-size: 14px;
}

.load-more-section {
  text-align: center;
  border-top: 1px solid #e9ecef;
  padding-top: 15px;
}

.load-more-btn {
  background: #28a745;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.load-more-btn:hover:not(.disabled) {
  background: #218838;
}

.load-more-btn.disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}
</style>
