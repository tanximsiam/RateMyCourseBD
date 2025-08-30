<script setup lang="ts">
import AdminReviewList from '@/components/AdminReviewList.vue'
import ProfileUpdater from '@/components/ProfileUpdater.vue'
</script>

<template>
  <div class="flex gap-10 mx-auto p-4">
    <AdminReviewList />
    <div class="flex flex-col gap-6 w-full">
      <ProfileUpdater />
    </div>
  </div>
</template>
