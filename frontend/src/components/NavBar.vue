<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import ProfileButton from '@/components/ProfileButton.vue'

const emit = defineEmits(['open-login'])

const auth = useAuthStore()
onMounted(() => auth.loadFromStorage())

const isLoggedIn = computed(() => !!auth.user)
const isAdmin = computed(() => auth.user?.role === 'admin')
</script>

<template>
  <nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <router-link to="/" class="text-xl font-bold text-blue-600 hover:text-blue-700">
      RateMyCourseBD
    </router-link>

    <div class="flex items-center gap-4">
      <!-- Admin buttons -->
       <router-link
        v-if="isAdmin"
        to="/reported-reviews"
        class="px-4 py-2 text-gray-800 rounded hover:bg-gray-200"
      >
        Reported Reviews
      </router-link>

      <router-link
        v-if="isAdmin"
        to="/suggested-courses"
        class="px-4 py-2 text-gray-800 rounded hover:bg-gray-200"
      >
        Suggested Courses
      </router-link>

      <router-link
        v-if="isAdmin"
        to="/course-outlines"
        class="px-4 py-2 text-gray-800 rounded hover:bg-gray-200"
      >
        Course Outlines
      </router-link>

      <!-- Login / Profile -->
      <button
        v-if="!isLoggedIn"
        @click="emit('open-login')"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Login
      </button>

      <ProfileButton v-else />
    </div>
  </nav>
</template>
