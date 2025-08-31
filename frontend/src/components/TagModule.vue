<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

type Tag = string

// v-model
const modelValue = defineModel<Tag[]>()

// ✅ Keep options inside the component
const OPTIONS: Tag[] = [
  'math-heavy','project-based','open-book','group work','attendance',
  'lab','mcq','conceptual','memorization','weekly quiz','heavy assignment',
  'presentation','research-oriented','coding','easy grader','tough grader','hard','easy',
  'theory','practical','interesting','boring','fun','challenging','straightforward',
  'interactive','monotonous','engaging','outdated','modern','fast-paced','slow-paced',
  'theoretical','application-based','comprehensive','concise','detailed','overview','in-depth',
  'supportive','unhelpful','accessible'
]

const query = ref('')
const open = ref(false)
const highlighted = ref(-1)
const root = ref<HTMLElement | null>(null)

const filtered = computed(() => {
  const q = query.value.toLowerCase().trim()
  const pool = OPTIONS.filter(o => !(modelValue.value ?? []).includes(o))
  return q ? pool.filter(o => o.toLowerCase().includes(q)) : pool
})

function add(tag: Tag) {
  if (!tag) return
  if (!OPTIONS.includes(tag)) return
  if ((modelValue.value ?? []).includes(tag)) return
  modelValue.value = [ ...(modelValue.value ?? []), tag ]
  query.value = ''
  highlighted.value = -1
  open.value = false
}

function remove(tag: Tag) {
  modelValue.value = (modelValue.value ?? []).filter(t => t !== tag)
}

function onInput() {
  open.value = true
  highlighted.value = filtered.value.length ? 0 : -1
}

function onKeydown(e: KeyboardEvent) {
  if (!open.value && (e.key === 'ArrowDown' || e.key === 'Enter')) {
    open.value = true
    highlighted.value = filtered.value.length ? 0 : -1
    return
  }
  if (!open.value) return
  if (e.key === 'ArrowDown') {
    e.preventDefault()
    if (!filtered.value.length) return
    highlighted.value = (highlighted.value + 1) % filtered.value.length
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    if (!filtered.value.length) return
    highlighted.value = (highlighted.value - 1 + filtered.value.length) % filtered.value.length
  } else if (e.key === 'Enter') {
    e.preventDefault()
    if (highlighted.value >= 0) add(filtered.value[highlighted.value])
  } else if (e.key === 'Escape') {
    open.value = false
  }
}

function onClickOutside(ev: MouseEvent) {
  if (!root.value) return
  if (!root.value.contains(ev.target as Node)) open.value = false
}

onMounted(() => document.addEventListener('mousedown', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('mousedown', onClickOutside))
</script>

<template>
  <div ref="root" class="space-y-2">
    <!-- Selected chips -->
    <div class="flex flex-wrap gap-2">
      <span
        v-for="tag in modelValue"
        :key="tag"
        class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full flex items-center"
      >
        {{ tag }}
        <button
          @click="remove(tag)"
          class="ml-2 text-red-500 hover:text-red-700"
          aria-label="Remove tag"
        >
          ×
        </button>
      </span>
    </div>

    <!-- Search / select -->
    <div class="relative">
      <input
        v-model="query"
        @input="onInput"
        @focus="onInput"
        @keydown="onKeydown"
        placeholder="Type to search…"
        class="w-full border p-2 rounded"
        autocomplete="off"
      />

      <ul
        v-if="open && filtered.length"
        class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded border bg-white shadow"
        role="listbox"
      >
        <li
          v-for="(opt, i) in filtered"
          :key="opt"
          :class="[
            'px-3 py-2 cursor-pointer',
            i === highlighted ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'
          ]"
          @mouseenter="highlighted = i"
          @mousedown.prevent="add(opt)"
          role="option"
          :aria-selected="i === highlighted"
        >
          {{ opt }}
        </li>
      </ul>

      <div
        v-else-if="open && !filtered.length"
        class="absolute z-10 mt-1 w-full rounded border bg-white p-2 text-sm text-gray-500"
      >
        No matches
      </div>
    </div>
  </div>
</template>
