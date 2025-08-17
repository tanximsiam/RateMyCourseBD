<template>
  <div class="flex items-center gap-3 py-1">
    <button
      :aria-pressed="localVote === 1"
      @click="onVote(1)"
      class="px-2 py-1 rounded hover:bg-gray-100"
      :class="localVote === 1 ? 'text-green-600' : 'text-gray-500'"
      title="Upvote"
    >
      ▲
    </button>

    <span class="text-sm font-semibold min-w-6 text-center text-gray-700">
      {{ score }}
    </span>

    <button
      :aria-pressed="localVote === -1"
      @click="onVote(-1)"
      class="px-2 py-1 rounded hover:bg-gray-100"
      :class="localVote === -1 ? 'text-red-600' : 'text-gray-500'"
      title="Downvote"
    >
      ▼
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import api from '@/services/api'

const props = defineProps<{
  reviewId: number
  myVote: 1 | -1 | null
  upvotes: number
  downvotes: number
  isLoggedIn?: boolean
}>()

const emit = defineEmits<{
  (e: 'changed', payload: { myVote: 1 | -1 | null; upvotes: number; downvotes: number }): void
}>()

const localVote = ref<1 | -1 | null>(props.myVote)
const ups = ref(props.upvotes)
const downs = ref(props.downvotes)

watch(() => props.myVote, v => (localVote.value = v))
watch(() => props.upvotes, v => (ups.value = v))
watch(() => props.downvotes, v => (downs.value = v))

const score = computed(() => ups.value - downs.value)
// const scoreClass = computed(() =>
//   score.value > 0 ? 'text-green-700' : score.value < 0 ? 'text-red-700' : 'text-gray-700'
// )

function adjustCounts(prev: 1 | -1 | null, next: 1 | -1 | null) {
  if (prev === null && next === 1) ups.value += 1
  if (prev === null && next === -1) downs.value += 1
  if (prev === 1 && next === null) ups.value -= 1
  if (prev === -1 && next === null) downs.value -= 1
  if (prev === 1 && next === -1) { ups.value -= 1; downs.value += 1 }
  if (prev === -1 && next === 1) { downs.value -= 1; ups.value += 1 }
}

async function onVote(clickVal: 1 | -1) {
  if (!props.isLoggedIn) {
    alert('Please log in to vote.')
    return
  }

  const prev = localVote.value
  const next: 1 | -1 | null = prev === clickVal ? null : clickVal

  adjustCounts(prev, next)
  localVote.value = next

  try {
    await api.post('/vote', { review_id: props.reviewId, vote: clickVal })
    emit('changed', { myVote: localVote.value, upvotes: ups.value, downvotes: downs.value })
  } catch {
    adjustCounts(next, prev)
    localVote.value = prev
  }
}
</script>
