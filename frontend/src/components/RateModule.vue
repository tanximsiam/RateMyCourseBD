<script setup lang="ts">
const rating = defineModel<number>()

function starColor(starIndex: number): string {
  if (!rating.value || rating.value < starIndex) return 'fill-gray-300'

  if (rating.value <= 2) return 'fill-red-400'
  if (rating.value === 3) return 'fill-yellow-400'
  return 'fill-green-400'
}
</script>

<template>
  <div class="flex gap-1">
    <button
      v-for="n in 5"
      :key="n"
      @click="rating = n"
      class="focus:outline-none"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-8 h-8"
        viewBox="0 0 24 24"
        :class="starColor(n)"
      >
        <path d="M12 .587l3.668 7.568 8.332 1.151-6.001 5.841 1.416 8.293L12 18.897l-7.415 4.543 1.416-8.293-6.001-5.841 8.332-1.151z" />
      </svg>
    </button>
  </div>
</template>
