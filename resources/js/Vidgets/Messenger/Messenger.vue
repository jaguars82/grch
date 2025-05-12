<template>
  <p>Сообщения</p>
  <div class="chat"></div>
  <q-input v-model="message" />
  <q-btn label="отправить" @click="sendMessage" />
</template>

<script>
import { ref } from 'vue'

export default {
  setup () {
    const message = ref(null)
    const sendMessage = () => {
      console.log(message.value)
      // Do things
    }
    return { message, sendMessage }
  }
}
</script>