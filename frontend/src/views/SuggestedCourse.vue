<template>
  <div class="flex gap-10 mx-auto p-4">
    <SuggestedCourseList />
    <div class="flex flex-col gap-6 w-full">
      <OutlineBox />
    </div>
  </div>
</template>
<script setup lang="ts">
import OutlineBox from '@/components/OutlineBox.vue'
import SuggestedCourseList from '@/components/SuggestedCourseList.vue'
</script>
