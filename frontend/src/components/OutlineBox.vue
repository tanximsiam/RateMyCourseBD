<template>
  <div class="p-4 bg-white rounded-2xl shadow">
    <h2 class="text-lg font-semibold mb-2">Course Outline</h2>

    <div v-if="outline">
      <a
        :href="outlineUrl"
        target="_blank"
        rel="noopener"
        class="text-blue-600 underline"
      >
        View Outline (PDF)
      </a>
    </div>

    <div v-else>
      <label class="block mb-2 font-medium">Upload PDF Outline:</label>
      <input
        type="file"
        accept="application/pdf"
        @change="handleFileChange"
        class="p-2 mr-4 bg-slate-100 outline-1"
      />
      <button
        @click="uploadOutline"
        :disabled="!file || isUploading"
        class="px-4 py-2 bg-blue-600 text-white rounded"
      >
        {{ isUploading ? 'Uploading...' : 'Submit Outline' }}
      </button>
    </div>

    <p v-if="error" class="text-red-600 mt-2">{{ error }}</p>
  </div>
  <div
    v-if="showLoginToast"
    class="fixed bottom-6 right-6 bg-yellow-600 text-white px-4 py-2 rounded shadow-lg z-50"
  >
    You need to log in first.
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'
import { AxiosError } from 'axios'

const route = useRoute()
const courseId = Number(route.params.id)

const outline = ref(null as null | { file_path: string })
const file = ref<File | null>(null)
const isUploading = ref(false)
const error = ref('')
const auth = useAuthStore()

const outlineUrl = computed(() =>
  outline.value
    ? `${import.meta.env.VITE_API_BASE_URL}/storage/${outline.value.file_path}`
    : ''
)

const showLoginToast = ref(false)

async function fetchOutline() {
  try {
    const res = await api.get(`/courses/${courseId}/outlines`)
    outline.value = res.data[0] || null
  } catch (err) {
    console.error('Failed to load outline', err)
  }
}

function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  if (target.files?.[0]) {
    file.value = target.files[0]
  }
}

async function uploadOutline() {
  if (!auth.token) {
    showLoginToast.value = true
    setTimeout(() => (showLoginToast.value = false), 2000)
    return
  }

  if (!file.value) return

  error.value = ''
  isUploading.value = true

  const formData = new FormData()
  formData.append('file', file.value)

  try {
    await api.post(`/courses/${courseId}/outline`, formData, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        'Content-Type': 'multipart/form-data',
      },
    })
    await fetchOutline()
    file.value = null
  } catch (err) {
    const axiosErr = err as AxiosError<{ message?: string }>
    error.value = axiosErr.response?.data?.message || 'Upload failed'
  } finally {
    isUploading.value = false
  }
}

onMounted(fetchOutline)
</script>
