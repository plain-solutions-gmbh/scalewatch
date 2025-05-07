import { defineStore } from 'pinia'

export const userStore = defineStore('user', {
  state: () => ({
    name: 'Guest',
    routine: null,
    task: null,
  }),
  actions: {
    login(name, routine) {
      this.name = name
      this.routine = routine
    },
    logout() {
      this.name = false
      this.routine = null
    },
    getData() {
      return { ...this.$state }
    },
  },
  getters: {
    isLogged: (state) => state.name !== null,
    isGuest: (state) => state.name === 'Guest',
  },
  persist: {
    storage: localStorage,
  },
})
