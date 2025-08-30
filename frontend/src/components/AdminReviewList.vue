<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'
import ReviewCard from './ReviewCard.vue'

const auth = useAuthStore()

type Review = {
  id: number
  rating: number
  review_text?: string
  tags?: string[]
  is_anonymous: boolean
  is_reported: boolean
  user: { id: number; name: string }
  course: { id: number; title: string }
  created_at: string
  upvotes: number
  downvotes: number
  my_vote: 1 | -1 | null
}

const reviews = ref<Review[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const searchQuery = ref('')

const filteredReviews = computed(() => {
  const q = searchQuery.value.toLowerCase()
  return reviews.value.filter(r =>
    r.course.title.toLowerCase().includes(q) ||
    r.user.name.toLowerCase().includes(q)
  )
})

async function fetchReportedReviews() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/reviews/reported')
    reviews.value = data
  } catch (err: unknown) {
    if (typeof err === 'object' && err !== null && 'response' in err) {
      const responseErr = err as { response?: { data?: { message?: string } } }
      error.value = responseErr.response?.data?.message ?? 'Failed to load reported reviews.'
    } else {
      error.value = 'Failed to load reported reviews.'
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

function onReviewDeleted(id: number) {
  reviews.value = reviews.value.filter(r => r.id !== id)
}

function onReviewKept(id: number) {
  reviews.value = reviews.value.filter(r => r.id !== id)
}

onMounted(() => {
  auth.loadFromStorage()
  fetchReportedReviews()
})
</script>

<template>
  <div>
    <div class="space-y-6 w-full">
      <div class="flex items-center w-full justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
          Reported Reviews
          <span v-if="!loading && !error" class="ml-2 text-sm text-gray-500">({{ filteredReviews.length }})</span>
        </h2>

        <input
          v-model="searchQuery"
          type="text"
          autocomplete="off"
          placeholder="Search by course or user…"
          class="border border-gray-300 rounded px-3 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div v-if="loading" class="text-gray-500">Loading reported reviews…</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>

      <div v-else class="flex flex-col gap-6 min-w-full">
        <ReviewCard
          v-for="review in filteredReviews"
          :key="review.id"
          :review="{ ...review, is_anonymous: false }"
          :inProfilePage="false"
          :canModerate="true"
          @update="onReviewUpdate"
          @deleted="onReviewDeleted"
          @kept="onReviewKept"
        />
        <div v-if="filteredReviews.length === 0" class="text-gray-500">
          No reported reviews found.
        </div>
      </div>
    </div>
  </div>
</template>
