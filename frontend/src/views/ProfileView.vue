<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import StudentProfile from '../components/StudentProfile.vue'
import AdminProfile from '../components/AdminProfile.vue'

const router = useRouter()
const auth = useAuthStore()
onMounted(() => {
  auth.loadFromStorage()
  if (!auth.user) {
    router.push('/')
  }
})


</script>

<template>
  <div class="mx-auto mt-10 px-4">
    <StudentProfile v-if="auth.isStudent" />
    <AdminProfile v-else-if="auth.isAdmin" />
    <div v-else class="text-center text-red-500 font-medium">
      Unknown role or not logged in.
    </div>
  </div>
</template>
