<script setup lang="ts">
import api from '../services/api'
import { useRouter } from 'vue-router'

const router = useRouter()

const handleLogout = async () => {
  try {
    await api.post('/logout')
    console.log('Logout successful')
  } catch (error) {
    console.error('Logout failed:', error)
  }

  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')

  router.push('/')
}
</script>

<template>
  <button
    @click="handleLogout"
    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
  >
    Logout
  </button>
</template>
