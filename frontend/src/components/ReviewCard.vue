<template>
  <div class=" bg-white shadow rounded-2xl px-8 py-6 flex flex-col justify-between items-center gap-6">
    <div class="w-full flex flex-col gap-1">
      <div class="text-2xl font-bold flex justify-between items-start">
        {{ review.is_anonymous ? 'Anonymous' : review.user.name }}
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
    <div v-if="showComments" class="w-full space-y-4">
      <CommentBox :review-id="review.id" @submitted="refreshAfterSubmit" />
      <div v-if="loadingComments" class="text-sm text-gray-500">Loading comments…</div>
      <CommentCards v-else :comments="comments" />
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

import TagChips from './TagChips.vue'
import VoteBox from './VoteBox.vue'
import CommentToggleButton from './CommentToggleButton.vue'
import CommentBox from './CommentBox.vue'
import CommentCards from './CommentCards.vue'


const auth = useAuthStore()

const props = defineProps({
  review: {
    type: Object,
    required: true
  }
})

const emit = defineEmits<{
  (e: 'update', payload: { id: number; my_vote: 1 | -1 | null; upvotes: number; downvotes: number }): void
}>()

function onVoteChanged(p: { myVote: 1 | -1 | null; upvotes: number; downvotes: number }) {
  emit('update', {
  id: props.review.id,
  my_vote: p.myVote,
  upvotes: p.upvotes,
  downvotes: p.downvotes
})
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

async function refreshAfterSubmit() {
  await loadComments()
}

</script>
