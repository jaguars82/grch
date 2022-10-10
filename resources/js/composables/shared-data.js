import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'

function userInfo() {
   const user = computed(() => usePage().props.value.auth.user)
   return { user }
}

function messagesAmount() {
   const messages = computed(() => usePage().props.value.messages)
   return { messages }
}

export { userInfo, messagesAmount }