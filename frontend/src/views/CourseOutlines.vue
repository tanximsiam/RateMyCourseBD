<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

type Row = {
  id: number
  course_id: number
  course?: string
  university?: string
  status: 'unapproved' | 'obsolete'
  file_path?: string
}

const loading = ref(true)
const error = ref('')
const unapproved = ref<Row[]>([])
const obsolete = ref<Row[]>([])
const busyId = ref<number | null>(null)

const tab = ref<'unapproved' | 'obsolete'>('unapproved')
const rows = computed(() => (tab.value === 'unapproved' ? unapproved.value : obsolete.value))

async function fetchList() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/admin/course-outlines/review')
    unapproved.value = data.unapproved ?? []
    obsolete.value = data.obsolete ?? []
  } catch (e) {
    error.value = 'Failed to load outlines'
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function keepOutline(id: number) {
  busyId.value = id
  try {
    await api.patch(`/admin/course-outlines/${id}/keep`)
    await fetchList()
  } finally {
    busyId.value = null
  }
}

async function deleteOutline(id: number) {
  if (!confirm('Delete this outline?')) return
  busyId.value = id
  try {
    await api.delete(`/admin/course-outlines/${id}`)
    await fetchList()
  } finally {
    busyId.value = null
  }
}

function fileHref(row: Row) {
  if (!row.file_path) return null

  // absolute URL? use as-is
  if (/^https?:\/\//i.test(row.file_path)) return row.file_path

  const base = (api.defaults as any)?.baseURL?.replace(/\/api\/?$/, '') ?? ''
  let path = row.file_path.startsWith('/') ? row.file_path : `/${row.file_path}`


  if (path.startsWith('/outlines/')) {
    path = '/storage' + path
  }

  return `${base}${path}`
}

onMounted(fetchList)
</script>

<template>
  <div class="p-4 bg-white rounded-2xl shadow">
    <h2 class="text-lg font-semibold mb-4">Outline Moderation</h2>

    <!-- Toggle -->
    <div class="mb-4 inline-flex rounded-lg overflow-hidden border">
      <button
        class="px-4 py-2"
        :class="tab === 'unapproved' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
        @click="tab = 'unapproved'"
      >
        Unapproved ({{ unapproved.length }})
      </button>
      <button
        class="px-4 py-2 border-l"
        :class="tab === 'obsolete' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
        @click="tab = 'obsolete'"
      >
        Obsolete ({{ obsolete.length }})
      </button>
    </div>

    <div v-if="loading" class="text-gray-600">Loading…</div>
    <div v-else>
      <p v-if="error" class="text-red-600 mb-3">{{ error }}</p>

      <div class="overflow-x-auto">
        <table class="min-w-full border">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 border text-left">University</th>
              <th class="p-2 border text-left">Course</th>
              <th class="p-2 border text-left">Status</th>
              <th class="p-2 border text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in rows" :key="row.id" class="border-t">
              <td class="p-2 border">{{ row.university ?? '-' }}</td>
              <td class="p-2 border">
                <template v-if="fileHref(row)">
                  <a
                    :href="fileHref(row)!"
                    target="_blank"
                    rel="noopener"
                    class="text-blue-600 underline"
                    title="Open outline file"
                  >
                    {{ row.course ?? `Course #${row.course_id}` }}
                  </a>
                </template>
                <template v-else>
                  <router-link :to="`/courses/${row.course_id}`" class="text-blue-600 underline">
                    {{ row.course ?? `Course #${row.course_id}` }}
                  </router-link>
                </template>
              </td>
              <td class="p-2 border capitalize">{{ row.status }}</td>
              <td class="p-2 border space-x-2">
                <button
                  class="px-3 py-1 bg-green-600 text-white rounded"
                  :disabled="busyId === row.id"
                  @click="keepOutline(row.id)"
                >
                  {{ busyId === row.id ? 'Saving…' : 'Keep' }}
                </button>
                <button
                  class="px-3 py-1 bg-red-600 text-white rounded"
                  :disabled="busyId === row.id"
                  @click="deleteOutline(row.id)"
                >
                  {{ busyId === row.id ? 'Deleting…' : 'Delete' }}
                </button>
              </td>
            </tr>

            <tr v-if="!rows.length">
              <td colspan="4" class="p-3 text-gray-500">
                No {{ tab }} outlines.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
