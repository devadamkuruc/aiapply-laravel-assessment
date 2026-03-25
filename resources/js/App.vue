<template>
  <div class="min-h-full">
    <!-- Navigation bar (only when authenticated) -->
    <nav v-if="isAuthenticated" class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Brand + nav links -->
          <div class="flex items-center space-x-8">
            <router-link to="/" class="text-xl font-bold text-primary-600 tracking-tight">
              TaskManager
            </router-link>
            <router-link
              to="/"
              class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors"
              :class="$route.path === '/' ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
            >
              Tasks
            </router-link>
            <router-link
              to="/tasks/create"
              class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors"
              :class="$route.path === '/tasks/create' ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
            >
              New Task
            </router-link>
          </div>

          <!-- User info + logout -->
          <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">{{ user?.name }}</span>
            <button
              @click="handleLogout"
              class="text-sm text-gray-500 hover:text-red-600 font-medium transition-colors px-3 py-1 rounded-md hover:bg-red-50"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Guest nav (login/register links) -->
    <nav v-else class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <span class="text-xl font-bold text-primary-600 tracking-tight">TaskManager</span>
          <div class="flex items-center space-x-4">
            <router-link
              to="/login"
              class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 px-4 py-2 rounded-md transition-colors"
            >
              Register
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main>
      <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <router-view />
      </div>
    </main>

    <!-- Toast notifications -->
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="flex items-start gap-3 bg-white border border-gray-200 rounded-lg shadow-lg px-4 py-3 w-80"
      >
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-semibold uppercase">
          {{ toast.initial }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900">{{ toast.name }} commented</p>
          <p class="text-xs text-gray-500 truncate">on "{{ toast.task }}"</p>
        </div>
        <button @click="dismiss(toast.id)" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from './composables/useAuth.js';
import { useToast } from './composables/useToast.js';

const router = useRouter();
const { isAuthenticated, user, logout } = useAuth();
const { toasts, show, dismiss } = useToast();

const currentUser = JSON.parse(localStorage.getItem('user') || 'null');

onMounted(() => {
  window.Echo.channel('comments').listen('CommentPosted', (e) => {
    if (e.user.id !== currentUser?.id) {
      show({ name: e.user.name, task: e.task_name });
    }
  });
});

onUnmounted(() => {
  window.Echo.leaveChannel('comments');
});

const handleLogout = async () => {
  await logout();
  router.push({ name: 'Login' });
};
</script>
