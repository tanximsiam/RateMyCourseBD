<template>
  <div>
    <div v-if="loading" class="text-gray-500">Loading reviews...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    <div v-else class="flex flex-col gap-6">
      <!-- Course Info Header -->
      <div class="px-8">
        <h2 class="text-2xl font-bold">{{ course?.title }}</h2>
        <p class="text-gray-600 text-sm">
          {{ course?.code }} · {{ course?.department?.name }} · {{ course?.university?.name }}
        </p>
      </div>

      <!-- Review Cards -->
      <ReviewCard
        v-for="review in reviews"
        :key="review.id"
        :review="review"
      />

      <div v-if="reviews.length === 0" class="text-gray-500">No reviews yet.</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import ReviewCard from './ReviewCard.vue'

const props = defineProps<{ reload: number }>() // ✅ Watch trigger from parent

const route = useRoute()
const courseId = Number(route.params.id)

type ReviewUser = {
  id: number
  name: string
}

type Review = {
  id: number
  rating: number
  review_text?: string
  tags?: string[]
  is_anonymous: boolean
  user: ReviewUser
  created_at: string
}

type Department = {
  id: number
  name: string
}

type University = {
  id: number
  name: string
}

type Course = {
  id: number
  title: string
  code: string
  department: Department
  university: University
}

const course = ref<Course | null>(null)
const reviews = ref<Review[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

type AxiosErrorResponse = {
  response?: {
    data?: {
      message?: string
    }
  }
}

const fetchReviews = async () => {
  loading.value = true
  try {
    const { data } = await api.get(`/courses/${courseId}/reviews/details`)
    course.value = data.course
    reviews.value = data.reviews
  } catch (err: unknown) {
    if (
      typeof err === 'object' &&
      err !== null &&
      'response' in err &&
      typeof (err as AxiosErrorResponse).response?.data?.message === 'string'
    ) {
      error.value = (err as AxiosErrorResponse).response!.data!.message!
    } else {
      error.value = 'Failed to load reviews.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(fetchReviews)
watch(() => props.reload, fetchReviews) // ✅ Refresh when reload changes
</script>
