<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

defineProps({ show: Boolean })
const emit = defineEmits(['close', 'success'])

// toggle
const mode = ref<'login'|'signup'>('login')

// ===== Login state (kept same) =====
const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })
    const token = response.data.token
    const user = response.data.user

    // keep exactly same storage + emits
    localStorage.setItem('auth_token', token)
    localStorage.setItem('user', JSON.stringify(user))

    emit('success', response.data.user || response.data)
    emit('close')
  } catch (err) {
    alert('Invalid credentials')
    console.error(err)
  }
}

// ===== Signup state =====
const name = ref('')
const suEmail = ref('')
const suPassword = ref('')
const suPasswordConfirm = ref('')
const universities = ref<Array<{ id: number; name: string; domain?: string }>>([])
const departments = ref<Array<{ id: number; name: string; university_id: number }>>([])
const universityId = ref<number | ''>('')
const departmentId = ref<number | ''>('')

const filteredDepartments = computed(() =>
  departments.value.filter(d => d.university_id === Number(universityId.value))
)

onMounted(async () => {
  try {
    const [uRes, dRes] = await Promise.all([
      api.get('/universities'),
      api.get('/departments'),
    ])
    universities.value = uRes.data
    departments.value = dRes.data
  } catch (e) {
    console.error(e)
  }
})

const creating = ref(false)
const signupError = ref('')

const handleSignup = async () => {
  signupError.value = ''
  if (suPassword.value !== suPasswordConfirm.value) {
    signupError.value = 'Passwords do not match'
    return
  }
  creating.value = true
  try {
    const response = await api.post('/register', {
      name: name.value.trim(),
      email: suEmail.value.trim(),
      password: suPassword.value,
      password_confirmation: suPasswordConfirm.value,
      university_id: Number(universityId.value),
      department_id: Number(departmentId.value),
    })

    const token = response.data.token
    const user = response.data.user

    // keep exactly same storage + emits
    localStorage.setItem('auth_token', token)
    localStorage.setItem('user', JSON.stringify(user))

    emit('success', response.data.user || response.data)
    emit('close')
  } catch (err: unknown) {
    // surface Laravel validation first if present
    const anyErr = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    const msg = anyErr?.response?.data?.message
    const errs = anyErr?.response?.data?.errors
    signupError.value = msg || (errs ? Object.values(errs).flat().join(' ') : 'Signup failed')
    console.error(err)
  } finally {
    creating.value = false
  }
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-lg">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">{{ mode === 'login' ? 'Login' : 'Sign up' }}</h2>
        <button type="button" @click="emit('close')" class="text-sm text-gray-500 hover:underline">Close</button>
      </div>

      <!-- Toggle -->
      <div class="grid grid-cols-2 gap-2 mb-6">
        <button
          class="py-2 rounded border"
          :class="mode==='login' ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100'"
          @click="mode='login'">
          Login
        </button>
        <button
          class="py-2 rounded border"
          :class="mode==='signup' ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100'"
          @click="mode='signup'">
          Sign up
        </button>
      </div>

      <!-- LOGIN -->
      <form v-if="mode==='login'" @submit.prevent="handleLogin" class="space-y-4">
        <input v-model="email" type="email" placeholder="Email" class="w-full border p-2 rounded" required />
        <input v-model="password" type="password" placeholder="Password" class="w-full border p-2 rounded" required />
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
          Login
        </button>

        <button type="button" @click="mode='signup'" class="w-full mt-2 text-sm text-blue-600 hover:underline">
          New here? Create an account
        </button>
      </form>

      <!-- SIGNUP -->
      <form v-else @submit.prevent="handleSignup" class="space-y-4">
        <input v-model="name" type="text" placeholder="Full Name" class="w-full border p-2 rounded" required />
        <input v-model="suEmail" type="email" placeholder="University Email" class="w-full border p-2 rounded" required />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <select v-model="universityId" class="w-full border p-2 rounded" required>
            <option value="" disabled selected>Select University</option>
            <option v-for="u in universities" :key="u.id" :value="u.id">{{ u.name }}</option>
          </select>

          <select v-model="departmentId" class="w-full border p-2 rounded" :disabled="!universityId" required>
            <option value="" disabled selected>Select Department</option>
            <option v-for="d in filteredDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <input v-model="suPassword" type="password" placeholder="Password" class="w-full border p-2 rounded" required />
          <input v-model="suPasswordConfirm" type="password" placeholder="Confirm Password" class="w-full border p-2 rounded" required />
        </div>

        <p v-if="signupError" class="text-red-600 text-sm">{{ signupError }}</p>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-60"
                :disabled="creating || !name || !suEmail || !universityId || !departmentId || !suPassword || suPassword!==suPasswordConfirm">
          {{ creating ? 'Creating account…' : 'Register' }}
        </button>

        <button type="button" @click="mode='login'" class="w-full mt-2 text-sm text-gray-500 hover:underline">
          Already have an account? Login
        </button>
      </form>
    </div>
  </div>
</template>
