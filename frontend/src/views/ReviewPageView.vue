<template>
  <div class="flex gap-10 mx-auto p-4">
    <!-- <h1 class="text-2xl font-bold mb-4">Course Reviews</h1> -->
    <ReviewCardBox :reload="reloadFlag" />
  <PostReviewBox @submitted="reloadFlag++" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PostReviewBox from '@/components/PostReviewBox.vue';
import ReviewCardBox from '../components/ReviewCardBox.vue'
const reloadFlag = ref(0)
</script>
