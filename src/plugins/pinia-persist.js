import localForage from 'localforage'
import { createPersistedState } from 'pinia-plugin-persistedstate'

export default ({ pinia }) => {
  pinia.use(
    createPersistedState({
      storage: {
        getItem: (key) => localForage.getItem(key),
        setItem: (key, value) => localForage.setItem(key, value),
        removeItem: (key) => localForage.removeItem(key),
      },
    }),
  )
}
