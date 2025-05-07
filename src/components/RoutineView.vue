<template>
  <div class="glide">
    <div class="glide__track" data-glide-el="track">
      <ul class="glide__slides">
        <li class="glide__slide center center max-height" v-for="scope in scopes" :key="scope.name">
          <h1 class="scope-title">{{ scope.name }}</h1>
          <div class="scope-container">
            <ScopeView
              v-if="values"
              :scope="scope"
              :parameters="routine?.parameters"
              :values="values"
            />
          </div>
        </li>
        <li v-if="result" class="glide__slide center center max-height">
          <h1 class="scope-title">Summary</h1>
          <div class="scope-container">
            <el-result :title="result.title" class="max-height">
              <template #sub-title>
                <p
                  v-for="item in result?.detail ?? []"
                  :key="item"
                  v-html="item"
                  style="margin: 0.5rem"
                ></p>
              </template>
              <template #icon>
                <component :is="result.icon" size="48px" />
              </template>
              <template #extra>
                <el-button
                  v-for="btn in result.buttons"
                  :key="btn"
                  :type="btn?.type ?? 'default'"
                  @click="btn.onClick"
                >
                  {{ btn.label }}</el-button
                >
              </template>
            </el-result>
          </div>
        </li>
      </ul>
    </div>

    <div v-if="scopes.length > 0" data-glide-el="controls" class="glide__controls">
      <button data-glide-dir="<"><RiArrowLeftSLine size="1.5rem" /></button>
      <button data-glide-dir=">"><RiArrowRightSLine size="1.5rem" /></button>
    </div>
  </div>
</template>

<script setup>
//Import glide
import Glide from '@glidejs/glide'
import '@glidejs/glide/dist/css/glide.core.min.css'
import '@glidejs/glide/dist/css/glide.theme.min.css'

import { useRoute, useRouter } from 'vue-router'

import request from '@/utils/api'
import { onMounted, ref, shallowRef } from 'vue'
import { configStore } from '@/stores'
import {
  RiQuestionLine,
  RiArrowRightSLine,
  RiArrowLeftSLine,
  RiErrorWarningLine,
} from '@remixicon/vue'

const config = configStore()

const route = useRoute()
const router = useRouter()
const index = route.params.index

const result = shallowRef(null)

const routine = ref([])
const scopes = ref([{ name: 'Wait...', rows: [] }])
const values = ref(null)

onMounted(async () => {
  request('routines', 'get', { index: index })
    .then((item) => {
      if (item.cta?.active !== true) {
        //Todo set processing if pendent
        item.scopes = null
        result.value = {
          icon: RiErrorWarningLine,
          title: 'Cannot continue',
          detail: [item.status_text],
          buttons: [
            {
              label: 'Go Back',
              type: 'warning',
              onClick: () => router.push('/'),
            },
          ],
        }
      } else {
        result.value = {
          icon: RiQuestionLine,
          title: item.name + ' done?',
          detail: item.checklist,
          buttons: [
            {
              label: 'Yes, Complete',
              type: 'primary',
              onClick: async () =>
                request('routines', 'setStatus', { index: index, status: 'done' }).then(() => {
                  router.push('/')
                }),
            },
            {
              label: 'No, Skip',
              type: 'warning',
              onClick: async () =>
                request('routines', 'setStatus', { index: index, status: 'skipped' }).then(() => {
                  router.push('/')
                }),
            },
          ],
        }
      }
      config.viewtitle = item.name
      routine.value = item
      return request('journal', 'get')
    })
    .then((val) => {
      values.value = val
      scopes.value = routine.value.scopes
    })
    .finally(() => {
      if (scopes.value.length > 0) {
        new Glide('.glide', {
          type: 'slider',
          startAt: 0,
          type: 'carousel',
        }).mount()
      }
    })
})
</script>

<style>
.scope-container {
  padding: 4em 1.5rem 1.5rem;
  overflow-x: auto;
  position: absolute;
  inset: 0;
}

.scope-title {
  position: absolute;
  line-height: 1;
  padding: 1rem 2.8rem;
  margin: 0;
  top: 0;
  left: 0;
  right: 0;
  text-align: center;
  background-color: var(--el-bg-color);
  z-index: 2;
}

.glide,
.glide__track,
.glide__slides,
.glide__slide {
  height: 100%;
}

.glide__slide {
  position: relative;
}
.glide__controls {
  position: absolute;
  pointer-events: none;
  display: flex;
  justify-content: space-between;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  border-top: 1px var(--el-border-color) var(--el-border-style);
}

.glide__controls > button {
  cursor: pointer;
  pointer-events: all;
  display: flex;
  justify-content: center;
  align-items: center;
  background: none;
  width: 3rem;
  border: none;
  aspect-ratio: 1 / 1;
  height: 3rem;
}

@media only screen and (max-width: 600px) {
  .scope-container {
    padding: 3em 1rem 1rem;
  }
}
</style>
