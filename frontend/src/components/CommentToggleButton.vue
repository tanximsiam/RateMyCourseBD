<script setup lang="ts">
defineProps<{ open: boolean }>()
const emit = defineEmits<{ (e: 'toggle'): void }>()
</script>

<template>
  <button
    type="button"
    @click="emit('toggle')"
    class="inline-flex items-center gap-1 rounded-md border px-3 py-1 text-sm hover:bg-gray-50"
  >
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor">
      <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z"/>
    </svg>
    <span>{{ open ? 'Hide comments' : 'Comment' }}</span>
  </button>
</template>
