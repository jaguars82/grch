<template>
  <div class="messenger row">
    <!-- Left panel -->
    <div class="left-bar col-4 q-pa-sm" v-if="aviableChats.length > 1">
      <ChatItem
        v-for="chat in aviableChats"
        :chat="chat"
        @click="selectChat(chat.id)"
        :isSelected="chat.id === currentChat"
      />
    </div>

    <!-- Active chat section -->
    <div :class="[aviableChats.length > 1 ? 'col-8' : 'col-12']" class="active-chat q-px-sm">
      <template v-if="currentChat > 0 || currentChat === 0">
        <Chat
          :chat-id="currentChat"
          :new-message="lastMessage"
          :newChatInterlocuter="currentChat === 0 && typeof newInterlocuter === 'object' ? newInterlocuter : null"
        />
        <q-input
          outlined
          autogrow
          dense
          v-model="message"
          placeholder="Введите сообщение"
        />
        <div class="text-right">
        <q-btn
          color="primary"
          padding="xs md"
          unelevated
          rounded
          label="отправить"
          icon="send"
          @click="sendMessage"
          :disabled="!message"
        />
        </div>
      </template>
      <div v-else class="q-py-lg text-center text-grey">
        Нет открытых чатов
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
//import axios from 'axios'
import { useAxiosRequest } from '@/composables/useAxiosRequest'
import { io } from 'socket.io-client'
import ChatItem from './partials/ChatItem.vue'
import Chat from './partials/Chat.vue'

export default {
  props: {
    userId: {
      type: Number,
    },
    interlocutorId: {
      type: [Number, null],
      default: null,
    },
    newInterlocuter: {
      type: [Object, null],
      default: null,
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
    chatDetails: {
      type: Object,
      default: {},
    },
  },
  components: {
    ChatItem, Chat,
  },
  setup(props, { emit }) {
    const currentMode = ref(props.mode)

    // A list of aviable chats
    const aviableChats = ref([])

    const url = window.location.href

    // A chat to open in the chat-window by default
    const currentChat = ref(null)

    const selectChat = (chatId) => {
      currentChat.value = chatId
    }

    watch(
      () => aviableChats.value,
      (chats) => {
        if (currentMode.value === 'url') {
          if (chats.length === 1) {
            currentChat.value = chats[0].id
          } else if (!chats.length && !props.isUserUrlAdmin) {
            currentChat.value = 0
          }
        }
      }, { deep: true, immediate: true, }
    )

    const newChatParams = computed (() => {
      if (currentChat.value === 0) {
        const params = {
          creator_id: props.userId,
          interlocuter_id: props.mode === 'url' ? props.urlAdminId : props.interlocutorId,
          is_url_attached: props.mode === 'url' ? true : false,
          url: url,
          title: 'title' in props.chatDetails && props.chatDetails.title ? props.chatDetails.title : '',
          type: 'private',
        }
        return params
      }
      return null
    })

    const { execute } = useAxiosRequest()

    onMounted(() => {
      // if in the 'url'-mode - try to find the attached chat
      if (currentMode.value === 'url') {
        execute(
          'post',
          `/vidgets/messenger/get-chat-by-url`,
          {
            url: url,
            params: props.urlParams,
            user_id: props.userId,
            is_url_admin: props.isUserUrlAdmin,
            url_admin_id: props.urlAdminId,
          },
          {
            onSuccess: (responseData) => {
              console.log(responseData);
              if (responseData.chats.length) {
                aviableChats.value = responseData.chats;
              }
            },
            onError: (error) => {
              console.log(error);
            }
          }
        )
      }
    })

    const message = ref('')
    const lastMessage = ref(null) // The last sended message
    
    let socket

    onMounted(() => {
      // Connecting to socket server (Socket.IO)
      socket = io('https://grch.ru:3000') // production
      // socket = io('http://dev.grch.ru:3000') // development

      // Handle recieve message
      socket.on('receiveMessage', (msg) => {
        if (msg.chat_id && currentChat.value === 0) {
          // aviableChats.value.push({ id: msg.chat_id })
          currentChat.value = msg.chat_id
        }

        if (msg.chat_id === currentChat.value || msg.token === newChatToken) {
          lastMessage.value = msg
        }
      })
    })

    onUnmounted(() => {
      socket.disconnect()
    })

    const newChatToken = ref(null)

    function generateRandomHash() {
      const chars = '0123456789abcdef';
      let hash = ''
      for (let i = 0; i < length; i++) {
          hash += chars[Math.floor(Math.random() * chars.length)]
      }
      return hash
    }

    // Send a message
    const sendMessage = () => {
      if (message.value.trim()) {
        newChatToken.value = generateRandomHash()

        socket.emit(
          'sendMessage', 
          {
            chat_id: currentChat.value,
            author_id: props.userId,
            text: message.value,
            chat_params: newChatParams.value,
            token: newChatToken.value,
          }
        )
        message.value = ''
      }
    }

    return {
      aviableChats,
      selectChat,
      currentChat,
      message,
      lastMessage,
      sendMessage,
    }
  }
};
</script>

<style scoped>
.active-chat {
  width: 420px;
}
.left-bar {
  width: 200px;
}
</style>