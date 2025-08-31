<template>
  <div class="p-4 bg-white rounded-2xl shadow">
    <h2 class="text-lg font-semibold mb-2">Course Outline</h2>

    <div v-if="status === 'approved'">
      <a
        :href="fileUrl"
        target="_blank"
        rel="noopener"
        class="text-blue-600 underline"
      >
        View Outline (PDF)
      </a>
      <button
        type="button"
        @click="reportOld"
        :disabled="isReporting"
        class="ml-3 px-3 py-1 bg-red-600 text-white rounded"
      >
        {{ isReporting ? 'Reporting...' : 'Report old' }}
      </button>
    </div>


    <div v-else-if="status === 'pending'" class="text-yellow-700 font-medium">
      Uploaded and waiting for approval.
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
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'
import { AxiosError } from 'axios'
const auth = useAuthStore()

const route = useRoute()
const courseId = Number(route.params.id)

const file = ref<File | null>(null)
const isUploading = ref(false)
const error = ref('')
const isReporting = ref(false)

const showLoginToast = ref(false)

const status = ref<'not_uploaded' | 'pending' | 'approved'>('not_uploaded')
const fileUrl = ref('')

async function fetchOutlineStatus() {
  try {
    const res = await api.get(`/courses/${courseId}/outline-status`)
    status.value = res.data.status
    if (res.data.status === 'approved') {
      fileUrl.value = res.data.file_url
    }
  } catch (err) {
    console.error('Failed to load outline status', err)
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
    await api.post(`/courses/${courseId}/outline`, formData)
    await fetchOutlineStatus()
    file.value = null
  } catch (err) {
    const axiosErr = err as AxiosError<{ message?: string }>
    error.value = axiosErr.response?.data?.message || 'Upload failed'
  } finally {
    isUploading.value = false
  }
}


async function reportOld() {
  if (!auth.token) {
    showLoginToast.value = true
    setTimeout(() => (showLoginToast.value = false), 2000)
    return
  }

  isReporting.value = true
  try {
    await api.post(`/courses/${courseId}/outline/report-old`)
    await fetchOutlineStatus()
  } catch (err) {
    console.error('Failed to report old outline', err)
  } finally {
    isReporting.value = false
  }
}


onMounted(fetchOutlineStatus)
</script>
