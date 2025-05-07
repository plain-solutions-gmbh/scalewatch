<template>
  <div class="overlay" v-if="message">
    <div class="center-center">
      <el-result icon="error" title="An error occures" :sub-title="message">
        <template #extra>
          <el-button type="primary" @click="reload()">Try reload</el-button>
        </template>
      </el-result>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { configStore } from '@/stores'

const config = configStore()

const message = computed(() => config.fatal)

const reload = () => {
  window.location.reload()
}
</script>
