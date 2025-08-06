<template>
  <v-select
    v-model="selected"
    :options="props.options"
    :label="props.label"
    :placeholder="props.placeholder"
    :disabled="props.disabled"
  />
</template>

<script setup lang="ts">
import 'vue-select/dist/vue-select.css'
import { computed, defineProps, defineEmits } from 'vue'

const props = defineProps({
  modelValue: Object,
  options: Array,
  label: String,
  placeholder: String,
  disabled: Boolean
})

const emit = defineEmits(['update:modelValue', 'change'])

const selected = computed({
  get: () => props.modelValue,
  set: (val) => {
    emit('update:modelValue', val)
    emit('change', val)
  }
})
</script>



