import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'

function userInfo() {
   const user = computed(() => usePage().props.value.auth.user)
   return { user }
}

export { userInfo }