<script setup lang="ts">
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

import InputBox from './InputBox.vue'
import RateModule from './RateModule.vue'
import TagModule from './TagModule.vue'
import PrimaryButton from './PrimaryButton.vue'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const showLoginToast = ref(false)

const emit = defineEmits(['submitted'])

const route = useRoute()
const courseId = parseInt(route.params.id as string)

const rating = ref<number>(0)
const reviewText = ref('')
const tags = ref<string[]>([])
const isAnonymous = ref(false)

const submitReview = async () => {
  if (!auth.token) {
    showLoginToast.value = true
    setTimeout(() => (showLoginToast.value = false), 2000)
    return
  }
  try {
    await api.post('/submit-review', {
      course_id: courseId,
      rating: rating.value,
      review_text: reviewText.value,
      tags: tags.value,
      is_anonymous: isAnonymous.value,
    })

    alert('Review submitted successfully')
    emit('submitted')

    rating.value = 0
    reviewText.value = ''
    tags.value = []
    isAnonymous.value = false
  } catch (err) {
    console.error(err)
    alert('Failed to submit review')
  }
}
</script>



<template>
  <div v-if="!auth.isAdmin" class="flex flex-col gap-6">
    <div class="px-8">
      <h2 class="text-2xl font-bold">Submit Review</h2>
      <p class="text-gray-600 text-sm">
        Your feedback helps others make informed decisions.
      </p>
    </div>
    <div class="min-w-xl p-8 rounded-xl space-y-4 bg-white shadow">
      <label class="block font-semibold text-lg">Rating</label>
      <RateModule v-model="rating" />

      <label class="block font-semibold text-lg">Review</label>
      <InputBox v-model="reviewText" placeholder="Write your review..." multiline />

      <label class="block font-semibold text-lg">Tags</label>
      <TagModule v-model="tags" />

      <div class="flex items-center gap-2">
        <input type="checkbox" v-model="isAnonymous" id="anon" />
        <label for="anon" class="text-sm text-gray-600">Submit anonymously</label>
      </div>

      <PrimaryButton @click="submitReview">Submit Review</PrimaryButton>
    </div>
  </div>
  <div
    v-if="showLoginToast"
    class="fixed bottom-6 right-6 bg-yellow-600 text-white px-4 py-2 rounded shadow-lg z-50"
  >
    You need to log in first.
  </div>
</template>
