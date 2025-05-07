import { serialize } from 'object-to-formdata'
import { userStore, configStore } from '@/stores'

let loading = null

const request = async function (modul, action, params = {}) {
  const loadingTimer = setTimeout(() => {
    loading = ElLoading.service({
      lock: true,
      text: 'Loading…',
      background: 'rgba(0, 0, 0, 0.7)',
    })
  }, 1000)

  const user = userStore()
  const config = configStore()

  const data = serialize({
    modul: modul,
    action: action,
    params: params,
    user: user.getData(),
  })

  try {
    const url = config.api
    const response = await fetch(url, {
      method: 'POST',
      body: data,
    })

    if (response.ok) {
      const res = await response.json()
      clearTimeout(loadingTimer)
      loading?.close()

      if (res.success) {
        return res.data
      }

      //Output error in console
      console.error(res.error)
      config.fatal = res.error.message

      // ElMessage.error({
      //   message: res.data.message,
      //   duration: 0
      // });
      // return
    }
  } catch (err) {
    console.error(err)
    loading.setText('Connection lost. Trying again…')

    //Retry conneting
    setTimeout(() => {
      request(modul, action, params)
    }, 2000)
  }
}

export default request
