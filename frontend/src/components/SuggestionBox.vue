<template>
  <button
      class="bg-slate-400 hover:bg-slate-500 text-white font-semibold py-2 px-4 rounded max-w-sm"
      @click="showForm = !showForm"
    >
      {{ showForm ? 'Hide Suggestion Form' : 'Suggest a New Course' }}
    </button>
  <div v-if="showForm" class="max-w-3xl mx-auto p-4 space-y-6">
    <VSelectWrapper
      v-model="selectedUniversity"
      :options="sortedUniversities"
      label="name"
      placeholder="Select University"
      @change="() => { selectedDepartment = undefined }"
    />

    <VSelectWrapper
      v-model="selectedDepartment"
      :options="departments"
      label="name"
      placeholder="Select Department"
      :disabled="!selectedUniversity"
    />

    <InputBox
      v-model="code"
      label="Course Code"
      placeholder="e.g. CSE420"
    />

    <InputBox
      v-model="title"
      label="Course Title"
      placeholder="e.g. Artificial Intelligence"
    />

    <InputBox
      v-model="creditsString"
      label="Credits"
      type="number"
      step="0.5"
      placeholder="e.g. 3.00"
    />

    <textarea
      v-model="description"
      class="w-full border border-gray-300 rounded p-2"
      rows="4"
      placeholder="Course Description (optional)"
    />

    <PrimaryButton
      label="Submit Suggestion"
      :disabled="!isValid"
      @click="submit"
    />

    <p v-if="message" class="text-green-600">{{ message }}</p>
    <p v-if="error" class="text-red-600">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import VSelectWrapper from './VSelectWrapper.vue'
import InputBox from './InputBox.vue'
import PrimaryButton from './PrimaryButton.vue'
import { isAxiosError } from 'axios'
import type { AxiosError } from 'axios'

type University = { id: number; name: string; departments: Department[] }
type Department = { id: number; name: string }

const universities = ref<University[]>([])
const selectedUniversity = ref<University | undefined>()
const selectedDepartment = ref<Department | undefined>()

const code = ref('')
const title = ref('')
const description = ref('')
const message = ref('')
const error = ref('')

const showForm = ref(false)

const credits = ref<number | null>(null)
const creditsString = computed({
  get: () => credits.value !== null ? credits.value.toString() : '',
  set: (val: string) => {
    credits.value = val ? parseFloat(val) : null
  }
})


const sortedUniversities = computed(() =>
  [...universities.value].sort((a, b) => a.name.localeCompare(b.name))
)

const departments = computed(() =>
  selectedUniversity.value
    ? [...selectedUniversity.value.departments].sort((a, b) => a.name.localeCompare(b.name))
    : []
)

const isValid = computed(() =>
  selectedUniversity.value &&
  selectedDepartment.value &&
  code.value &&
  title.value
)

const fetchUniversities = async () => {
  const { data } = await api.get('/universities')
  universities.value = data
}

const submit = async () => {
  message.value = ''
  error.value = ''
  try {
    await api.post('/suggestions', {
      university_id: selectedUniversity.value?.id,
      department_id: selectedDepartment.value?.id,
      code: code.value,
      title: title.value,
      credits: credits.value,
      description: description.value,
    })
    message.value = 'Suggestion submitted successfully.'
    code.value = ''
    title.value = ''
    credits.value = null
    description.value = ''
  } catch (err: unknown) {
    if (isAxiosError(err)) {
      const axiosErr = err as AxiosError<{ message?: string }>
      error.value = axiosErr.response?.data?.message || 'Submission failed.'
    } else {
      error.value = 'Unexpected error.'
    }
  }
}



onMounted(fetchUniversities)
</script>
