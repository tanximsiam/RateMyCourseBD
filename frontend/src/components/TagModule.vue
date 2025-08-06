<script setup lang="ts">
import { ref } from 'vue'

const modelValue = defineModel<string[]>()
const newTag = ref('')

const addTag = () => {
  const tag = newTag.value.trim()
  if (tag && !(modelValue.value ?? []).includes(tag)) {
    modelValue.value = [...(modelValue.value ?? []), tag]
  }
  newTag.value = ''
}

const removeTag = (tag: string) => {
  modelValue.value = (modelValue.value ?? []).filter(t => t !== tag)
}
</script>

<template>
  <div class="space-y-2">
    <div class="flex gap-2">
      <input
        v-model="newTag"
        @keyup.enter="addTag"
        placeholder="Type and press Enter"
        class="flex-1 border p-2 rounded"
      />
      <button
        @click="addTag"
        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"
      >
        Add
      </button>
    </div>

    <div class="flex flex-wrap gap-2">
      <span
        v-for="tag in modelValue"
        :key="tag"
        class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full flex items-center"
      >
        {{ tag }}
        <button
          @click="removeTag(tag)"
          class="ml-2 text-red-500 hover:text-red-700"
        >
          x
        </button>
      </span>
    </div>
  </div>
</template>
