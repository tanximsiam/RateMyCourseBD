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

  </div>
</template>

<script setup lang="ts">
import TagChips from './TagChips.vue'
defineProps({
  review: {
    type: Object,
    required: true
  }
})
</script>
