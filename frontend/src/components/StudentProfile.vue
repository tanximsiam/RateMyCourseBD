<script setup lang="ts">
import StudentReviewList from '@/components/StudentReviewList.vue'
import ProfileUpdater from '@/components/ProfileUpdater.vue'
</script>

<template>
  <div class="flex gap-10 mx-auto p-4">
    <StudentReviewList />
    <div class="flex flex-col gap-6 w-full">
      <ProfileUpdater />
    </div>
  </div>
</template>
