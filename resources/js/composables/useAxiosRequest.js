import { ref } from 'vue'
import axios from 'axios'

export function useAxiosRequest() {
  const isLoading = ref(false)
  const error = ref(null)
  const data = ref(null)

  const execute = async (method = 'post', url, payload, { onSuccess, onError } = {}) => {
    isLoading.value = true
    error.value = null
    data.value = null
    
    try {
      const response = await axios({
        method: method.toLowerCase(),
        url,
        data: payload
      })
      
      data.value = response.data
      
      if (onSuccess) {
        onSuccess(response.data)
      } else {
        return response.data
      }
    } catch (err) {
      error.value = err
      
      if (onError) {
        onError(err)
      } else {
        console.error('Axios request failed:', err)
      }
      
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    execute,
    isLoading,
    error,
    data,
  }
}