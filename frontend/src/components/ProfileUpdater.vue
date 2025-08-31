<!-- ProfileUpdater.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

type MeResponse = {
  id: number
  name: string
  email: string
  role: 'student' | 'admin'
  // optional shapes coming from your backend:
  university?: { id: number; name: string }
  student?: { id: number; university?: { id: number; name: string } }
}

const auth = useAuthStore()

const me = ref<MeResponse | null>(null)
const loadingProfile = ref(true)
const showPwdForm = ref(false)
const pwdLoading = ref(false)
const pwdError = ref<string | null>(null)
const pwdSuccess = ref<string | null>(null)

const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirm = ref('')

const role = computed(() => me.value?.role ?? auth.user?.role ?? 'student')
const name = computed(() => me.value?.name ?? auth.user?.name ?? '')
const universityName = computed(() => {
  // Try common shapes: top-level university OR nested under student
  return me.value?.university?.name ?? me.value?.student?.university?.name ?? null
})

async function loadMe() {
  loadingProfile.value = true
  try {
    // Adjust this endpoint to your actual "who am I" route
    const { data } = await api.get('/me')
    me.value = data as MeResponse
  } catch {
    // fall back to auth store if /me is unavailable
    me.value = auth.user as unknown as MeResponse
  } finally {
    loadingProfile.value = false
  }
}

function togglePwdForm() {
  showPwdForm.value = !showPwdForm.value
  if (!showPwdForm.value) {
    resetPwdForm()
  }
}

function resetPwdForm() {
  currentPassword.value = ''
  newPassword.value = ''
  newPasswordConfirm.value = ''
  pwdError.value = null
  pwdSuccess.value = null
}

async function updatePassword() {
  pwdError.value = null
  pwdSuccess.value = null

  if (!currentPassword.value || !newPassword.value || !newPasswordConfirm.value) {
    pwdError.value = 'All password fields are required.'
    return
  }
  if (newPassword.value !== newPasswordConfirm.value) {
    pwdError.value = 'New password and confirmation do not match.'
    return
  }

  pwdLoading.value = true
  try {
    // Adjust endpoint & payload to your backend (common shape below)
    await api.post('/me/change-password', {
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: newPasswordConfirm.value,
    })
    pwdSuccess.value = 'Password updated successfully.'
    // Close the form after a short delay
    setTimeout(() => {
      togglePwdForm()
    }, 300)
  } catch (err: unknown) {
    if (typeof err === 'object' && err !== null && 'response' in err) {
      const res = (err as { response?: { data?: { message?: string } } }).response
      pwdError.value = res?.data?.message ?? 'Failed to update password.'
    } else {
      pwdError.value = 'Failed to update password.'
    }
  } finally {
    pwdLoading.value = false
  }
}

onMounted(() => {
  auth.loadFromStorage()
  loadMe()
})
</script>

<template>
  <div class="bg-white shadow rounded-2xl p-6 space-y-5 w-full min-w-lg">
    <h2 class="text-xl font-bold text-gray-800">Profile</h2>

    <div v-if="loadingProfile" class="text-gray-500">Loading profile…</div>

    <div v-else class="space-y-2">
      <div class="flex items-center justify-between">
        <span class="text-gray-600">Name</span>
        <span class="font-medium text-gray-900">{{ name }}</span>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-gray-600">Role</span>
        <span
          class="px-2 py-0.5 rounded-2xl text-sm font-medium"
          :class="role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'"
        >
          {{ role }}
        </span>
      </div>

      <!-- Only for students: show university -->
      <div v-if="role === 'student' && universityName" class="flex items-center justify-between">
        <span class="text-gray-600">University</span>
        <span class="font-medium text-gray-900">{{ universityName }}</span>
      </div>
    </div>

    <!-- Change Password -->
    <div class="pt-4 border-t">
      <button
        class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900"
        @click="togglePwdForm"
      >
        {{ showPwdForm ? 'Cancel' : 'Change Password' }}
      </button>

      <form
        v-if="showPwdForm"
        @submit.prevent="updatePassword"
        autocomplete="off"
        class="mt-4 space-y-3"
      >
        <div class="flex flex-col gap-1">
          <label class="text-sm text-gray-600">Current Password</label>
          <input
            v-model="currentPassword"
            type="password"
            autocomplete="current-password"
            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="flex flex-col gap-1">
          <label class="text-sm text-gray-600">New Password</label>
          <input
            v-model="newPassword"
            type="password"
            autocomplete="new-password"
            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="flex flex-col gap-1">
          <label class="text-sm text-gray-600">Confirm New Password</label>
          <input
            v-model="newPasswordConfirm"
            type="password"
            autocomplete="new-password"
            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div v-if="pwdError" class="text-sm text-red-600">{{ pwdError }}</div>
        <div v-if="pwdSuccess" class="text-sm text-green-600">{{ pwdSuccess }}</div>

        <div class="flex justify-end">
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-60"
            :disabled="pwdLoading"
          >
            {{ pwdLoading ? 'Updating…' : 'Update Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
