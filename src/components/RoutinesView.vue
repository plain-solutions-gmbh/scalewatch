<template>
  <el-table :data="data" style="width: 100%">
    <el-table-column type="expand">
      <template #default="props">
        <el-descriptions direction="vertical" style="margin: -1rem 0" :column="2" border>
          <el-descriptions-item :rowspan="3" label="Checklist">
            <p v-for="item in props.row.checklist" style="margin: 0" :key="item">{{ item }}</p>
          </el-descriptions-item>
          <el-descriptions-item label="Status">{{ props.row.status_text }}</el-descriptions-item>
          <el-descriptions-item label="Routine">{{ props.row.routine }}</el-descriptions-item>
        </el-descriptions>
      </template>
    </el-table-column>
    <el-table-column label="Routine" min-width="200">
      <template #default="scope">
        <div style="display: flex; align-items: center">
          <span class="custom-icon" :style="{ backgroundImage: iconStyle(scope.row.icon) }"></span>
          <span style="margin-left: 10px">{{ scope.row.name }}</span>
        </div>
      </template>
    </el-table-column>

    <el-table-column label="Action" width="150">
      <template #default="scope">
        <el-button
          :style="{ width: '100%' }"
          :type="scope.row.cta.type"
          :disabled="!scope.row.cta.active"
          @click="start(scope.row.index)"
          >{{ scope.row.cta.label }}</el-button
        >
      </template>
    </el-table-column>
  </el-table>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import request from '@/utils/api'

const router = useRouter()

const data = ref([])

onMounted(async () => {
  data.value = await request('routines')
})

const iconStyle = (icon) => 'url(\"' + icon + '\")'

const start = async (index) => {
  data.value = await request('routines', 'setStatus', { index: index, status: 'processing' })
  router.push({ name: 'routine', params: { index: index } })
}
</script>

<style>
.custom-icon {
  height: 1rem;
  width: 1rem;
}
</style>
