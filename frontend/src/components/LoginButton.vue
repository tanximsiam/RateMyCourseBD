<script setup lang="ts">
import { ref } from 'vue'
import LoginModal from './LoginModal.vue'

const showModal = ref(false)

const handleSuccess = (userData: []) => {
  console.log('Logged in user:', userData)
  showModal.value = false
}
</script>

<template>
  <div>
    <button
      @click="showModal = true"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
    >
      Login
    </button>

    <LoginModal
      v-if="showModal"
      :show="showModal"
      @close="showModal = false"
      @success="handleSuccess"
    />
  </div>
</template>
