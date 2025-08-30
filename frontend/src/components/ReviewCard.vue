<template>
  <div class="bg-white shadow rounded-2xl px-8 py-6 flex flex-col justify-between items-center gap-6">
    <div class="w-full flex flex-col gap-1">
      <div class="text-2xl font-bold flex justify-between items-start">
        <span>
          <span v-if="inProfilePage">
            {{ review.course?.title ?? 'Course' }}
          </span>
          <span v-else>
            {{ review.is_anonymous ? 'Anonymous' : review.user.name }}
          </span>
        </span>
        <span
          class="text-lg font-semibold px-4 py-2 rounded-2xl"
          :class="{
            'bg-red-200': review.rating <= 2,
            'bg-yellow-200': review.rating === 3,
            'bg-green-200': review.rating >= 4
          }"
        >
          Rating: {{ review.rating }}
        </span>
      </div>
      <TagChips v-if="review.tags?.length" :tags="review.tags" />
    </div>

    <div v-if="review.review_text" class="text-gray-700 text-xl mb-2">
      <!-- {{ review.review_text }} -->
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quasi est non, ratione error magni omnis maxime vero animi voluptas commodi eaque distinctio unde iusto, eveniet cum, voluptatum perferendis iure. Et?
        Accusamus, veritatis porro. Totam commodi vero voluptatibus nobis repudiandae animi vel quos, possimus libero earum natus labore at architecto sint magni tenetur reprehenderit ea excepturi ullam quam. Doloremque, rem nobis.
    </div>

    <div class="flex items-center gap-6 text-gray-600">
      <VoteBox
        :review-id="review.id"
        :my-vote="review.my_vote ?? null"
        :upvotes="review.upvotes ?? 0"
        :downvotes="review.downvotes ?? 0"
        :is-logged-in="!!auth.token"
        @changed="onVoteChanged"
      />
      <CommentToggleButton :open="showComments" @toggle="toggleComments" />
    </div>
    <div class="w-full text-right mt-2 flex justify-end gap-4 text-sm">
      <button
        v-if="canDelete"
        class="text-red-500 hover:underline"
        @click="deleteReview"
      >
        Delete
      </button>
      <button
        v-if="canReport"
        class="text-gray-500 hover:underline"
        :disabled="reporting || reportSuccess"
        @click="reportReview">
        Report
      </button>
      <button
        v-if="showKeep"
        class="text-blue-500 hover:underline"
        :disabled="unreporting"
        @click="keepReview"
      >
        Keep
      </button>
    </div>
    <div v-if="showComments" class="w-full space-y-4">
      <CommentBox :review-id="review.id" @submitted="refreshAfterSubmit" />
      <div v-if="loadingComments" class="text-sm text-gray-500">Loading comments…</div>
      <CommentCards v-else :comments="comments" />
    </div>
    <div
      v-if="showDeleteSuccess"
      class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
    >
      Review deleted successfully.
    </div>
    <div
      v-if="reportSuccess"
      class="fixed bottom-6 right-6 bg-yellow-600 text-white px-4 py-2 rounded shadow-lg z-50"
    >
      Review reported successfully.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

import TagChips from './TagChips.vue'
import VoteBox from './VoteBox.vue'
import CommentToggleButton from './CommentToggleButton.vue'
import CommentBox from './CommentBox.vue'
import CommentCards from './CommentCards.vue'

const auth = useAuthStore()

type ReviewUser = { id: number; name: string }
type ReviewCourse = { title: string }

const props = withDefaults(defineProps<{
  review: {
    id: number
    rating: number
    review_text?: string
    tags?: string[]
    is_anonymous: boolean
    user: ReviewUser
    course?: ReviewCourse
    created_at: string
    upvotes: number
    downvotes: number
    my_vote: 1 | -1 | null
    is_reported: boolean
  },
  inProfilePage?: boolean
  canModerate?: boolean
}>(), {
  inProfilePage: false,
  canModerate: false
})

const emit = defineEmits<{
  (e: 'update', payload: { id: number; my_vote: 1 | -1 | null; upvotes: number; downvotes: number }): void
  (e: 'deleted', reviewId: number): void
  (e: 'reported', reviewId: number): void
  (e: 'kept', reviewId: number): void
}>()

function onVoteChanged(p: { myVote: 1 | -1 | null; upvotes: number; downvotes: number }) {
  emit('update', {
    id: props.review.id,
    my_vote: p.myVote,
    upvotes: p.upvotes,
    downvotes: p.downvotes
  })
}

const isAdmin = computed(() => auth.user?.role === 'admin')

const canDelete = computed(() =>
  !!auth.user && (auth.user.id === props.review.user?.id || isAdmin.value)
)

const canReport = computed(() =>
  !!auth.user &&
  auth.user.id !== props.review.user?.id &&
  !isAdmin.value &&
  !props.canModerate
)

const showKeep = computed(() =>
  isAdmin.value && props.canModerate && props.review.is_reported
)

const showDeleteSuccess = ref(false)

async function deleteReview() {
  if (!confirm('Are you sure you want to delete this review?')) return
  try {
    await api.delete(`/reviews/${props.review.id}`)
    showDeleteSuccess.value = true
    emit('deleted', props.review.id)
    setTimeout(() => {
      showDeleteSuccess.value = false
      window.location.reload()
    }, 1500)
  } catch (err) {
    console.error('Delete failed:', err)
  }
}

type Comment = {
  id: number
  user_id: number
  review_id: number
  content: string
  created_at: string
  user?: { id: number; name: string }
}

const showComments = ref(false)
const loadingComments = ref(false)
const comments = ref<Comment[]>([])

async function loadComments() {
  loadingComments.value = true
  try {
    const { data } = await api.get(`/reviews/${props.review.id}/comments`)
    comments.value = data
  } finally {
    loadingComments.value = false
  }
}

async function toggleComments() {
  showComments.value = !showComments.value
  if (showComments.value && comments.value.length === 0) {
    await loadComments()
  }
}

const reporting = ref(false)
const reportSuccess = ref(false)

async function reportReview() {
  if (!confirm('Are you sure you want to report this review?')) return
  reporting.value = true
  try {
    await api.post(`/reviews/${props.review.id}/report`)
    reportSuccess.value = true
    emit('reported', props.review.id)
    setTimeout(() => {
      reportSuccess.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to report review:', err)
  } finally {
    reporting.value = false
  }
}

const unreporting = ref(false)

async function keepReview() {
  if (!confirm('Mark this review as safe?')) return
  unreporting.value = true
  try {
    await api.post(`/admin/reviews/${props.review.id}/keep`)
    emit('kept', props.review.id)
  } catch (err) {
    console.error('Failed to keep review:', err)
  } finally {
    unreporting.value = false
  }
}

async function refreshAfterSubmit() {
  await loadComments()
}
</script>
