<template>
  <el-scrollbar height="100%" :view-style="{ height: '100%' }">
    <el-menu style="height: 100%" :default-active="$route.path" router>
      <el-menu-item
        v-for="(item, i) in navitems"
        :index="item.path"
        :style="{ color: item.meta?.color ?? '#FFFFFF' }"
      >
        <template #title>
          <el-space :size="2">
            <i :class="'ri-' + item.meta.icon + '-line  ri-lg'">&nbsp;</i>{{ item.meta.title }}
          </el-space>
        </template>
      </el-menu-item>
    </el-menu>
  </el-scrollbar>
</template>

<script>
export default {
  computed: {
    routes() {
      return this.$router.getRoutes()
    },
    navitems() {
      return this.routes.filter((a) => a.meta.shownav === true)
    },
  },
}
</script>
