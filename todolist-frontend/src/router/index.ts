import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { requiresGuest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { requiresGuest: true }
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
      meta: { requiresAuth: true }
    },
  ],
})

// Guard de autenticação
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Verificar autenticação se o token existir mas o usuário não
  if (authStore.token && !authStore.user) {
    await authStore.checkAuth()
  }
  
  const requiresAuth = to.meta.requiresAuth
  const requiresGuest = to.meta.requiresGuest
  const isAuthenticated = authStore.isAuthenticated
  
  if (requiresAuth && !isAuthenticated) {
    // Rota protegida e usuário não autenticado
    next('/login')
  } else if (requiresGuest && isAuthenticated) {
    // Rota para visitantes e usuário já autenticado
    next('/')
  } else {
    next()
  }
})

export default router
