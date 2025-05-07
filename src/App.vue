<style>
@import '@/assets/normalize.css';
@import '@/assets/main.css';
@font-face {
  font-family: Hind;
  src: url('@/assets/fonts/Hind-SemiBold.ttf');
  font-weight: bold;
}
@font-face {
  font-family: Hind;
  src: url('@/assets/fonts/Hind-Light.ttf');
  font-weight: light;
}
@font-face {
  font-family: Hind;
  src: url('@/assets/fonts/Hind-Medium.ttf');
  font-weight: medium;
}
@font-face {
  font-family: Hind;
  src: url('@/assets/fonts/Hind-Regular.ttf');
  font-weight: normal;
}
@font-face {
  font-family: Hind;
  src: url('@/assets/fonts/Hind-Bold.ttf');
  font-weight: black;
}
body {
  font-family: Hind;
}
</style>

<template>
  <FatalView />

  <RouterView v-if="route.meta.fullscreen ?? false" />

  <el-container v-else>
    <el-header style="display: flex; align-items: center">
      <el-page-header @back="onBack" style="width: 100%">
        <template #content>
          <div class="flex items-center">
            <span class="text-large font-600 mr-3">{{ pageTitle }}</span>
          </div>
        </template>
        <template #extra>
          <div class="flex items-center">
            <el-tag size="large">{{ user.name }}</el-tag>
          </div>
        </template>
      </el-page-header>
    </el-header>
    <el-container style="height: 100%">
      <!-- <el-aside width="150px" style="height: 100%">
        <AsideNav />
      </el-aside> -->
      <el-main style="padding: 0"><RouterView /></el-main>
    </el-container>
  </el-container>
</template>

<script setup async>
import { userStore } from '@/stores'
import { useRoute, useRouter } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()
const router = useRouter()
const user = userStore()

const onBack = () => {
  const target = route.path === '/' ? '/login' : '/'
  router.push(target)
}

/* Watch pagetitle */

import { configStore } from '@/stores'
const config = configStore()
const pageTitle = computed(() => config.viewtitle)
</script>
