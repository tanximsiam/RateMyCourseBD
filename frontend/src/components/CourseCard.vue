<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'

type Uni = { id: number; name: string }
type Dept = { id: number; name: string }

const props = defineProps<{
  suggestion: {
    id: number
    university: Uni
    department?: Dept
    user_id: number
    code: string  // "course id" per your wording
    title: string
    credits?: number | null
    description?: string | null
    approved: boolean
    created_at?: string
  }
}>()

const emit = defineEmits<{
  (e: 'deleted', suggestionId: number): void
  (e: 'kept', suggestionId: number): void
}>()

const deleting = ref(false)
const keeping = ref(false)
const showDeleteSuccess = ref(false)
const showKeepSuccess = ref(false)

async function deleteSuggestion() {
  if (!confirm('Delete this suggested course?')) return
  deleting.value = true
  try {
    await api.delete(`/course-suggestions/${props.suggestion.id}`)
    showDeleteSuccess.value = true
    emit('deleted', props.suggestion.id)
    setTimeout(() => (showDeleteSuccess.value = false), 1500)
  } catch (e) {
    console.error('Delete failed:', e)
  } finally {
    deleting.value = false
  }
}

async function keepSuggestion() {
  if (!confirm('Keep this suggestion and create course entry?')) return
  keeping.value = true
  try {
    await api.post(`/course-suggestions/${props.suggestion.id}/approve`)
    showKeepSuccess.value = true
    emit('kept', props.suggestion.id)
    setTimeout(() => (showKeepSuccess.value = false), 1500)
  } catch (e) {
    console.error('Keep failed:', e)
  } finally {
    keeping.value = false
  }
}
</script>

<template>
  <div class="bg-white shadow rounded-2xl px-8 py-6 flex flex-col justify-between items-center gap-6">
    <!-- Header -->
    <div class="w-full flex flex-col gap-1">
      <div class="text-2xl font-bold flex justify-between items-start text-nowrap">
        <span class="mr-6">
          {{ suggestion.title }}
        </span>
        <span class="text-lg font-semibold px-4 py-2 rounded-2xl bg-blue-100">
          Code: {{ suggestion.code }}
        </span>
      </div>
      <div class="text-sm text-gray-600 text-nowrap">
        <span class="font-medium">University:</span>
        <span>{{ suggestion.university?.name ?? '—' }}</span>
        <span v-if="suggestion.department" class="ml-2">•</span>
        <span v-if="suggestion.department" class="ml-1">
          <span class="font-medium">Department:</span> {{ suggestion.department.name }}
        </span>
        <span v-if="suggestion.credits != null" class="ml-2">•</span>
        <span v-if="suggestion.credits != null" class="ml-1">
          <span class="font-medium">Credits:</span> {{ suggestion.credits }}
        </span>
      </div>
    </div>

    <!-- Description -->
    <div v-if="suggestion.description" class="text-gray-700 text-xl mb-2 w-full">
      {{ suggestion.description }}
    </div>

    <!-- Actions -->
    <div class="w-full text-right mt-2 flex justify-end gap-4 text-sm">
      <button
        class="text-red-500 hover:underline disabled:opacity-50"
        :disabled="deleting || keeping"
        @click="deleteSuggestion"
      >
        Delete
      </button>
      <button
        class="text-blue-500 hover:underline disabled:opacity-50"
        :disabled="keeping || deleting"
        @click="keepSuggestion"
      >
        Keep
      </button>
    </div>

    <!-- Toasts -->
    <div
      v-if="showDeleteSuccess"
      class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
    >
      Suggestion deleted.
    </div>
    <div
      v-if="showKeepSuccess"
      class="fixed bottom-6 right-6 bg-blue-600 text-white px-4 py-2 rounded shadow-lg z-50"
    >
      Course created from suggestion.
    </div>
  </div>
</template>
