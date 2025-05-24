<template>
  <Chat
    :new-message="lastMessage"
  />
  <q-input v-model="message" placeholder="Введите сообщение" />
  <q-btn label="отправить" @click="sendMessage" />
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { io } from 'socket.io-client'
import Chat from './partials/Chat.vue'

export default {
  props: {
    userId: {
      type: Number,
    },
    mode: {
      type: String,
      default: 'free', // 'free' - common mode, 'url' - url attached mode
    },
    urlParams: {
      type: Array,
      default: [],
    },
    isUserUrlAdmin: {
      type: Boolean,
      default: false,
    },
    urlAdminId: Number,
  },
  components: {
    Chat, /*Message,*/
  },
  setup(props, { emit }) {
    const currentMode = ref(props.mode)

    // A list of aviable chats
    const initialChatList = ref([])

    // A chat to open in the chat-window by default
    const initialChat = computed(() => {
      if (initialChatList.value.length === 1) {
        return initialChatList.value[0].id
      }
      return null
    })

    onMounted(() => {
      // if in the 'url'-mode - try to find the attached chat
      if (currentMode.value === 'url') {
        const url = window.location.href
        axios.post(`/vidgets/messenger/get-chat-by-url`, {
          url: url,
          params: props.urlParams,
          user_id: props.userId,
          is_url_admin: props.isUserUrlAdmin,
          url_admin_id: props.urlAdminId,
        })
        .then(function (response) {
          console.log(response.data)
          if (response.data.chats.lenght) {
            initialChatList.value = response.data.chats
          }
        })
        .catch(function (error) {
          console.log(error)
        })
      }
    })

    const message = ref('')
    const lastMessage = ref(null) // The last sended message
    
    let socket

    onMounted(() => {
      // Connecting to socket server (Socket.IO)
      socket = io('http://dev.grch.ru:3000')

      // Handle recieve message
      socket.on('receiveMessage', (msg) => {
        lastMessage.value = msg
      })
    })

    onUnmounted(() => {
      socket.disconnect()
    })

    // Send a message
    const sendMessage = () => {
      if (message.value.trim()) {
        socket.emit('sendMessage', message.value)
        message.value = ''
      }
    }

    return {
      initialChatList,
      initialChat,
      message,
      lastMessage,
      sendMessage,
    }
  }
};
</script>