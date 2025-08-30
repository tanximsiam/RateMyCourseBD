<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import ProfileButton from '@/components/ProfileButton.vue'

const emit = defineEmits(['open-login'])

const auth = useAuthStore()
onMounted(() => auth.loadFromStorage())

const isLoggedIn = computed(() => !!auth.user)
</script>

<template>
  <nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <router-link to="/" class="text-xl font-bold text-blue-600 hover:text-blue-700">
      RateMyCourseBD
    </router-link>

    <div class="flex items-center gap-4">
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
