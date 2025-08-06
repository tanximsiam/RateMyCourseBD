<template>
  <div class="min-w-3xl mx-auto p-4 space-y-6">
    <VSelectWrapper
      v-model="selectedUniversity"
      :options="sortedUniversities"
      label="name"
      placeholder="Select University"
      @change="() => { selectedDepartment = undefined; selectedCourse = undefined }"
    />

    <VSelectWrapper
      v-model="selectedDepartment"
      :options="departments"
      label="name"
      placeholder="Select Department"
      :disabled="!selectedUniversity"
      @change="() => { selectedCourse = undefined }"
    />

    <VSelectWrapper
      v-model="selectedCourse"
      :options="courses"
      label="title"
      placeholder="Select Course"
      :disabled="!selectedDepartment"
    />

    <button
      class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded"
      :disabled="!selectedCourse"
      @click="goToCourse"
    >
      View Reviews
    </button>
  </div>
</template>


<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import VSelectWrapper from './VSelectWrapper.vue'

type Course = {
  id: number
  code: string
  title: string
  department_id: number
}

type Department = {
  id: number
  name: string
}

type University = {
  id: number
  name: string
  departments: Department[]
  courses: Course[]
}


const universities = ref<University[]>([])
const selectedUniversity = ref<University | undefined>()
const selectedDepartment = ref<Department | undefined>()
const selectedCourse = ref<Course | undefined>()



const router = useRouter()

const sortedUniversities = computed(() =>
  [...universities.value].sort((a, b) => a.name.localeCompare(b.name))
)

const departments = computed(() =>
  selectedUniversity.value
    ? [...selectedUniversity.value.departments].sort((a, b) => a.name.localeCompare(b.name))
    : []
)

const courses = computed(() => {
  if (!selectedUniversity.value || !selectedDepartment.value) return []
  return selectedUniversity.value.courses
    .filter(c => c.department_id === selectedDepartment.value!.id)
    .sort((a, b) => a.title.localeCompare(b.title))
})

const fetchUniversities = async () => {
  const { data } = await api.get('/universities')
  universities.value = data
}

onMounted(fetchUniversities)


const goToCourse = async () => {
  if (selectedCourse.value) {
    router.push({ name: 'CourseReviews', params: { id: selectedCourse.value.id } })
  }
}
</script>
