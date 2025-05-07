<template>
  <div v-if="scope?.rows">
    <el-row v-for="row in props.scope.rows" :key="row" v-bind="scope">
      <el-col v-for="col in row.cols" :key="col" v-bind="col">
        <ItemView v-bind="col" :parameters="props.parameters" :values="props.values" />
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  scope: {
    type: Object,
    required: false,
  },
  parameters: {
    type: Array,
    required: false,
  },
  values: {
    type: Object,
    required: true,
  },
})
</script>

<style></style>
