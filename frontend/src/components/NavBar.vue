<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const emit = defineEmits(['open-login'])

const user = ref<{ name: string } | null>(null)

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
})

const isLoggedIn = computed(() => !!user.value)

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  location.reload() // reload to reflect logout
}
</script>

<template>
  <nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <router-link to="/"
       class="text-xl font-bold text-blue-600 hover:text-blue-700">
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

      <div v-else class="flex items-center gap-3">
        <img
          src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ user?.name }}"
          alt="avatar"
          class="w-8 h-8 rounded-full border"
        />
        <span class="font-medium text-gray-700">{{ user?.name }}</span>
        <button @click="logout" class="text-sm text-red-500 hover:underline">Logout</button>
      </div>
    </div>
  </nav>
</template>
