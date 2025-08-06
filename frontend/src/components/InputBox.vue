<script setup lang="ts">
const modelValue = defineModel<string>()
defineProps<{
  placeholder?: string
  multiline?: boolean
}>()
</script>

<template>
  <textarea
    v-if="multiline"
    v-model="modelValue"
    :placeholder="placeholder"
    rows="4"
    class="w-full border p-2 rounded"
  />
  <input
    v-else
    v-model="modelValue"
    :placeholder="placeholder"
    class="w-full border p-2 rounded"
  />
</template>
