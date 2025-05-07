<template>
  <div class="item">
    <h3>{{ props.name }}</h3>

    <el-form
      class="item-form"
      :model="values"
      label-width="auto"
      :label-position="isMobile ? 'top' : 'left'"
      :inline
    >
      <el-form-item v-for="input in parameters" :label="input.name" :key="input">
        <el-input-number
          class="item-input"
          v-if="rawValues"
          v-bind="input"
          v-model="rawValues[props.tank][input.id]"
          @change="(value) => change(value, props.tank, input.id, input?.default ?? 0)"
          :style="{ color: input?.color ?? '#000' }"
        >
          <template #suffix>{{ input.unit }}</template>
        </el-input-number>
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue'
import request from '@/utils/api'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'

const breakpoints = useBreakpoints(breakpointsTailwind)

const isMobile = breakpoints.smaller('md')

const props = defineProps({
  id: String,
  name: String,
  tank: String,
  margin: Boolean,
  parameters: Array,
  values: {
    type: Object,
    default: () => {},
  },
})

const rawValues = ref(null)
const initValues = ref(JSON.parse(JSON.stringify(props.values)))

onMounted(() => {
  rawValues.value = JSON.parse(JSON.stringify(props.values))
})

const change = (newValue, tank, key, def) => {
  if (initValues.value[tank][key] === null) {
    newValue = def
  }

  rawValues.value[tank][key] = newValue
  initValues.value[tank][key] = newValue

  request('journal', 'set', { tank: tank, key: key, value: newValue })
}
</script>

<style>
.item {
  padding: 1.5rem;
  margin-bottom: 1rem;
  border-radius: 0.3rem;
  border: 1px solid #3399ff;
}

.item-input .el-input__wrapper {
  border: solid 1px currentColor;
}

.item-input input {
  color: #fff;
}

.item-form > div:last-child {
}
</style>
