// If you work with vite:
// - Run 'run dev dev:sync' to sync api folder
// - Set your localhost in '.enc.development'

const params = {
  api: import.meta.env.VITE_API_URL
}

import { createApp } from 'vue'

import App from './App.vue'
const app = createApp(App)

/* Import Icons */
import 'remixicon/fonts/remixicon.css'

/* Import Element Plus */
import ElementPlus from 'element-plus'
import 'element-plus/theme-chalk/dark/css-vars.css'
app.use(ElementPlus, { zIndex: 3000 })

/* Import Router */
import { createWebHistory, createRouter } from 'vue-router'
import { routes } from '@/routes'
const router = createRouter({
  history: createWebHistory(),
  routes,
})
app.use(router)

/* Import Pinia */
import { createPinia } from 'pinia'
import setupPiniaPersist from '@/plugins/pinia-persist'
const pinia = createPinia()
setupPiniaPersist({ pinia })
app.use(pinia)

/* Load config from server */
import { configStore } from '@/stores'
const config = configStore()
config.init(params.api)

/* Set pagetitle */

router.beforeEach((to) => {
  config.viewtitle = to.meta.title
})

/* Forward to login */
import { userStore } from '@/stores'
const user = userStore()
if (user.isLogged === false) {
  router.push('/login')
}

app.mount('#app')
