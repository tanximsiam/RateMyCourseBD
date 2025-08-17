<script setup lang="ts">
import { ref } from 'vue'
import { isAxiosError } from 'axios'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

const props = defineProps<{ reviewId: number }>()
const emit = defineEmits<{ (e: 'submitted'): void }>()
const auth = useAuthStore()

const content = ref('')
const posting = ref(false)
const info = ref('')
const error = ref('')

async function submit() {
  info.value = ''
  error.value = ''
  const text = content.value.trim()
  if (!text) return
  if (!auth.user) { info.value = 'Please log in to comment.'; return }

  posting.value = true
  try {
    await api.post(`/reviews/${props.reviewId}/comments`, { content: text })
    content.value = ''
    emit('submitted')
  } catch (err) {
    if (isAxiosError(err)) {
      if (err.response?.status === 409) info.value = 'You already commented on this review.'
      else if (err.response?.status === 401) info.value = 'Please log in to comment.'
      else error.value = err.response?.data?.message || 'Failed to post comment.'
    } else error.value = 'Failed to post comment.'
  } finally {
    posting.value = false
  }
}
</script>

<template>
  <div class="rounded-lg border p-3 space-y-2">
    <textarea
      v-model="content"
      class="w-full rounded-md border p-2"
      rows="3"
      placeholder="Add a comment…"
      :disabled="posting"
    />
    <div class="flex items-center justify-between">
      <div class="text-sm">
        <span v-if="info" class="text-blue-600">{{ info }}</span>
        <span v-else-if="error" class="text-red-600">{{ error }}</span>
      </div>
      <button
        type="button"
        class="inline-flex items-center rounded-md bg-black px-3 py-2 text-white disabled:opacity-50"
        :disabled="posting || !content.trim()"
        @click="submit"
        aria-label="Submit comment"
        title="Submit comment"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 12h14.586l-4.293-4.293 1.414-1.414L22.414 14l-7.707 7.707-1.414-1.414L17.586 13H3z"/>
        </svg>
      </button>
    </div>
  </div>
</template>
