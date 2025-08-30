<!-- ProfileButton.vue -->
<template>
  <div class="relative" ref="dropdownRef">
    <button
      class="rounded-full px-3 py-1 text-2xl font-medium flex items-center gap-3"
      @click="open = !open"
    >
      {{ userName }}
      <img
        :src="`https://api.dicebear.com/7.x/thumbs/svg?seed=${encodeURIComponent(userName)}`"
        alt="avatar"
        class="h-10 w-10 rounded-full border"
      />
    </button>

    <ul
      v-if="open"
      class="absolute right-0 mt-2 w-40 bg-white border rounded shadow z-50 text-base"
    >
      <li>
        <button
          class="w-full text-left px-4 py-2 hover:bg-gray-100 text-slate-900 font-medium"
          @click="goProfile"
        >
          Profile
        </button>
      </li>
      <li>
        <button
          class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600 font-medium"
          @click="doLogout"
        >
          Logout
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const props = withDefaults(defineProps<{
  profileRoute?: string
}>(), {
  profileRoute: '/profile',
})

const router = useRouter()
const auth = useAuthStore()

const open = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const userName = computed(() => auth.user?.name ?? 'User')

function goProfile() {
  open.value = false
  router.push(props.profileRoute)
}

function doLogout() {
  open.value = false
  auth.logout()
  // optional: force a clean state if needed
  location.reload()
}

function handleClickOutside(event: MouseEvent) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    open.value = false
  }
}

onMounted(() => {
  auth.loadFromStorage()
  document.addEventListener('click', handleClickOutside)
})
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>
