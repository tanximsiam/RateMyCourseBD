<!-- SuggestedCourseList.vue -->
<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import api from '@/services/api'
import CourseCard from './CourseCard.vue'

const props = defineProps<{ reload?: number }>() // optional trigger to refetch

type Uni = { id: number; name: string }
type Dept = { id: number; name: string }

export type CourseSuggestion = {
  id: number
  university: Uni
  department?: Dept
  user_id: number
  code: string
  title: string
  credits?: number | null
  description?: string | null
  approved: boolean
  created_at?: string
}

const suggestions = ref<CourseSuggestion[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

async function fetchSuggestions() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/course-suggestions')
    suggestions.value = Array.isArray(data) ? data : (data?.data ?? [])
  } catch (err: unknown) {
    if (typeof err === 'object' && err !== null && 'response' in err) {
      const axiosErr = err as { response?: { data?: { message?: string } } }
      error.value = axiosErr.response?.data?.message ?? 'Failed to load course suggestions.'
    } else {
      error.value = 'Failed to load course suggestions.'
    }
  } finally {
    loading.value = false
  }
}

function onKept(id: number) {
  // Remove from list once created in courses table
  suggestions.value = suggestions.value.filter(s => s.id !== id)
}

function onDeleted(id: number) {
  suggestions.value = suggestions.value.filter(s => s.id !== id)
}

onMounted(fetchSuggestions)
watch(() => props.reload, fetchSuggestions)
</script>

<template>
  <div>
    <div v-if="loading" class="text-gray-500">Loading suggestions...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>

    <div v-else class="flex flex-col gap-6">
      <div class="px-8">
        <h2 class="text-2xl font-bold">Suggested Courses</h2>
        <p class="text-gray-600 text-sm">Review, keep to create, or delete.</p>
      </div>

      <CourseCard
        v-for="s in suggestions"
        :key="s.id"
        :suggestion="s"
        @kept="onKept"
        @deleted="onDeleted"
      />

      <div v-if="suggestions.length === 0" class="text-gray-500 px-8">
        No pending suggestions.
      </div>
    </div>
  </div>
</template>
