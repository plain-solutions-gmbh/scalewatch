import request from '@/utils/api'
import { defineStore } from 'pinia'

export const configStore = defineStore('config', {
  state: () => ({
    api: '/',
    params: {},
    users: [],
    routines: [],
    viewtitle: 'ScaleWatch',
    fatal: null,
  }),
  actions: {
    //Get config from server
    async init(api) {
      this.api = api
      this.$patch(await request('config'))
    },
  },
})
