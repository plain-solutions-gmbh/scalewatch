<template>
  <div class="navbar">
    <el-button class="navbar--button" type="danger" :style="{ borderRadius: 0 }">
      <i class="ri-close-circle-line ri-xl"></i>
    </el-button>

    <div class="navbar--title"></div>

    <el-button class="navbar--button" type="success" :style="{ borderRadius: 0 }">
      <i class="ri-checkbox-circle-line ri-xl"></i>
    </el-button>
  </div>

  <el-divider class="navbar--divider" />
</template>

<style>
.navbar {
  display: flex;
  justify-content: stretch;
}

.navbar--title {
  flex: 1 1 100%;
}

.navbar--divider {
  margin: 0;
}

.navbar--button {
  border-radius: none;
}
</style>
