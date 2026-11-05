<script setup>
import { computed } from 'vue'
import { STATUS } from '../format'

const props = defineProps({ status: { type: String, required: true } })
const info = computed(() => STATUS[props.status] ?? { label: props.status, tone: 'wait' })
</script>

<template>
  <span :class="['badge', `badge--${info.tone}`]">
    <span class="badge__dot" aria-hidden="true"></span>
    {{ info.label }}
  </span>
</template>
