<script setup lang="ts">
import { ref } from 'vue'
import api from '../services/api'

defineProps({ show: Boolean })
const emit = defineEmits(['close', 'success'])

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    const token = response.data.token
    const user = response.data.user

    // Store token and user
    localStorage.setItem('auth_token', token)
    localStorage.setItem('user', JSON.stringify(user))

    emit('success', response.data.user || response.data)
    emit('close')
  } catch (err) {
    alert('Invalid credentials')
    console.error(err)
  }
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-sm">
      <h2 class="text-xl font-bold mb-4 text-center">Login</h2>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <input v-model="email" type="email" placeholder="Email" class="w-full border p-2 rounded" />
        <input v-model="password" type="password" placeholder="Password" class="w-full border p-2 rounded" />

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
          Login
        </button>
        <button type="button" @click="emit('close')" class="w-full mt-2 text-sm text-gray-500 hover:underline">
          Cancel
        </button>
      </form>
    </div>
  </div>
</template>
