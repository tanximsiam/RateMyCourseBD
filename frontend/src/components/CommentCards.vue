<script setup lang="ts">
type Comment = {
  id: number
  user_id: number
  review_id: number
  content: string
  created_at: string
  user?: { id: number; name: string }
}
defineProps<{ comments: Comment[] }>()
</script>

<template>
  <div class="space-y-3">
    <div
      v-for="c in comments"
      :key="c.id"
      class="rounded-lg border p-3"
    >
      <div class="mb-1 text-sm text-gray-600">
        <span class="font-medium">{{ c.user?.name ?? 'User' }}</span>
        <span class="mx-1">•</span>
        <span>{{ new Date(c.created_at).toLocaleString() }}</span>
      </div>
      <div class="whitespace-pre-wrap">{{ c.content }}</div>
    </div>
    <div v-if="comments.length === 0" class="text-sm text-gray-500">
      No comments yet.
    </div>
  </div>
</template>
