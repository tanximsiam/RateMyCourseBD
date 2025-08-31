<template>
  <div>
    <div v-if="loading" class="text-gray-500">Loading reviews...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    <div v-else class="flex flex-col gap-6">
      <!-- Course Info Header -->
      <div class="px-8 space-y-1">
        <h2 class="text-2xl font-bold">{{ course?.title }}</h2>
        <p class="text-gray-600 text-sm">
          {{ course?.code }} · {{ course?.department?.name }} · {{ course?.university?.name }}
        </p>

        <div class="text-sm text-gray-700 mt-1">
          <div v-if="averageRating !== null">
            <strong>Average Rating:</strong> {{ averageRating.toFixed(1) }} ★
          </div>
          <div v-if="topTags.length">
            <strong class="mr-2">Top Tags:</strong>
            <span
              v-for="[tag, count] in topTags"
              :key="tag"
              class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full mr-2 mt-1"
            >
              {{ tag }} ({{ count }})
            </span>
          </div>
        </div>
      </div>

      <!-- Review Cards -->
      <ReviewCard
        v-for="review in reviews"
        :key="review.id"
        :review="review"
        @update="onReviewUpdate"
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
const averageRating = ref<number | null>(null)
const topTags = ref<[string, number][]>([])

type ReviewUser = {
  id: number
  name: string
}

type ReviewsResponse = {
  course: Course
  reviews: Review[]
  average_rating: number
  top_tags: Record<string, number>
}

type Review = {
  id: number
  rating: number
  review_text?: string
  tags?: string[]
  is_anonymous: boolean
  user: ReviewUser
  created_at: string
  upvotes: number
  downvotes: number
  my_vote: 1 | -1 | null
  is_reported: boolean
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
  error.value = null
  try {
    const { data } = await api.get<ReviewsResponse>(`/courses/${courseId}/reviews/details`)

    course.value = data.course
    reviews.value = (data.reviews || []).map((r) => ({
      ...r,
      is_reported: r.is_reported ?? false,
    }))
    averageRating.value = data.average_rating ?? null
    topTags.value = Object.entries(data.top_tags || {})
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

function onReviewUpdate(p: { id: number; my_vote: 1 | -1 | null; upvotes: number; downvotes: number }) {
  const review = reviews.value.find(r => r.id === p.id)
  if (review) {
    review.my_vote = p.my_vote
    review.upvotes = p.upvotes
    review.downvotes = p.downvotes
  }
}

onMounted(fetchReviews)
watch(() => props.reload, fetchReviews) // ✅ Refresh when reload changes
</script>
