<template>
  <div class="center-center">
    <el-form label-width="auto" style="max-width: 600px">
      <el-form-item label="User">
        <el-select v-model="user.name" placeholder="Select" style="width: 240px">
          <el-option v-for="user in users" :key="user" :value="user" />
        </el-select>
      </el-form-item>
      <el-form-item v-if="!user.isGuest" label="Routine">
        <el-select v-model="user.routine" placeholder="Select" style="width: 240px">
          <el-option v-for="routine in routines" :key="routine" :value="routine" />
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" style="width: 100%" @click="onSubmit">Login</el-button>
      </el-form-item>
    </el-form>
    <div style="padding-top: 2rem; text-align: center">
      <p>
        <a href="https://github.com/plain-solutions-gmbh/scalewatch" target="_blank"
          >Manual & Download</a
        >
      </p>
      <p><a href="/data" target="_blank">Data Folder</a></p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { userStore, configStore } from '@/stores'
import { useRouter } from 'vue-router'

const user = userStore()
const config = configStore()

const users = computed(() => config.users)
const routines = computed(() => {
  const items = Object.keys(config.routines)
  return items.filter((key) => key !== 'General')
})

const router = useRouter()

const onSubmit = () => {
  router.push('/')
}
</script>
