<template>
  <div class="flex gap-10 mx-auto p-4">
    <!-- <h1 class="text-2xl font-bold mb-4">Course Reviews</h1> -->
    <ReviewCardBox :reload="reloadFlag" />
    <div class="flex flex-col gap-6 w-full">
      <PostReviewBox @submitted="reloadFlag++" />
      <OutlineBox />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PostReviewBox from '@/components/PostReviewBox.vue'
import ReviewCardBox from '@/components/ReviewCardBox.vue'
import OutlineBox from '@/components/OutlineBox.vue'
const reloadFlag = ref(0)
</script>
