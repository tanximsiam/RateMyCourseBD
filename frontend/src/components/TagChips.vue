<script setup lang="ts">
defineProps<{
  tags: string[]
}>()
</script>

<template>
  <div class="flex flex-wrap gap-2 mt-2">
    <span
      v-for="tag in tags"
      :key="tag"
      class="bg-blue-100 text-slate-800 text-sm font-medium px-3 py-1 rounded-full"
    >
      {{ tag }}
    </span>
  </div>
</template>
