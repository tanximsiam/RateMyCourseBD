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
  user: { id: number; name: string }
  created_at: string
  upvotes: number
  downvotes: number
  my_vote: 1 | -1 | null
  course: {
    id: number
    title: string
  }
}

const reviews = ref<Review[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const searchQuery = ref('')

const filteredReviews = computed(() =>
  reviews.value.filter(r =>
    r.course.title.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

async function fetchMyReviews() {
  loading.value = true
  try {
    const { data } = await api.get('/me/reviews') // 🔄 Your backend should support this route
    reviews.value = data
  } catch (err: unknown) {
    if (typeof err === 'object' && err !== null && 'response' in err) {
      const responseErr = err as { response?: { data?: { message?: string } } }
      error.value = responseErr.response?.data?.message ?? 'Failed to load reviews.'
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

const showToast = ref(false)

function onReviewDeleted(id: number) {
  reviews.value = reviews.value.filter(r => r.id !== id)
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
    window.location.reload()
  }, 1500)
}

onMounted(() => {
  auth.loadFromStorage()
  fetchMyReviews()
})


</script>

<template>
  <div>
    <div class="space-y-6 w-full">
      <div class="flex items-center w-full justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Your Reviews</h2>
        <input
          v-model="searchQuery"
          type="text"
          autocomplete="off"
          placeholder="Search by course..."
          class="border border-gray-300 rounded px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div v-if="loading" class="text-gray-500">Loading reviews...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>

      <div v-else class="flex flex-col gap-6 min-w-full">
        <ReviewCard
          v-for="review in filteredReviews"
          :key="review.id"
          :review="review"
          :inProfilePage="true"
          @update="onReviewUpdate"
          @deleted="onReviewDeleted"
        />
        <div v-if="filteredReviews.length === 0" class="text-gray-500">
          No matching reviews found.
        </div>
      </div>
      <div
        v-if="showToast"
        class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
      >
        Review deleted successfully.
      </div>
    </div>
  </div>
</template>
