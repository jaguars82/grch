<template>
  <div class="chat">
    <div>
    </div>
    <p>Сообщения</p>
    <div class="messages-container">
      <Message v-for="msg in messages" :message="msg" />
    </div>
  </div>
</template>

<script>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import Message from './Message.vue'

export default {
  props: {
    chatId: {
      type: [Number, null],
      default: null,
    },
    newMessage: {
      type: [Object, String],
      default: null,
    },
  },
  components: {
    Message,
  },
  setup (props) {
    const messages = ref([])

    watch(() => props.newMessage, (msg) => {
      if (msg) {
        messages.value.push(msg)
      }
    })

    return {
      messages
    }
  }
}
</script>

<style scoped>
.messages-container {
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 10px;
}
</style>