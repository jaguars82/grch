<template>
  <div class="chat">
    <div v-if="interlocuter" class="q-py-sm">
      <UserInfoBar :user="interlocuter" />
    </div>
    <div
      ref="messagesContainer"
      class="q-py-sm q-px-md q-mb-md rounded-borders bg-grey-3 messages-container"
    >
      <Loading v-if="isLoading" size="md" />
      <template v-if="messages.length">
        <Message v-for="msg in messages" :message="msg" />
      </template>
      <div v-else class="q-py-lg text-center text-grey">
        Здесь пока нет сообщений
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch, onMounted, nextTick } from 'vue'
import Message from './Message.vue'
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import Loading from '@/Components/Elements/Loading.vue'
import { useAxiosRequest } from '@/composables/useAxiosRequest'
import { userInfo } from '@/composables/shared-data'

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
    newChatInterlocuter: {
      type: [Object, null],
      default: null,
    },
  },
  components: {
    Message, UserInfoBar, Loading,
  },
  setup (props) {
    const { user } = userInfo()

    const chat = ref(null)
    const interlocuter = ref(props.newChatInterlocuter && typeof props.newChatInterlocuter === 'object' ? props.newChatInterlocuter : null)
    const messages = ref([])

    watch(() => props.newMessage, (msg) => {
      if (msg) {
        messages.value.push(msg)
        scrollToLastMessage()
      }
    })

    const { execute, isLoading } = useAxiosRequest()

    const fetchMessages = async () => {
      if (props.chatId > 0) {
        await execute(
          'post',
          '/vidgets/messenger/get-chat-messages',
          { id: props.chatId },
          {
            onSuccess: (response) => {
              messages.value = response
              scrollToLastMessage()
            },
            onError: (err) => {
              console.error('axios request failed:', err)
            }
          }
        )
      }
    }

    const fetchInterlocuter = async () => {
      if (typeof chat.value === 'object' && chat.value.type === 'private' && chat.value.interlocuter_id) {
        const interlocuterField = user.id === chat.value.creator_id ? 'interlocuter_id' : 'creator_id'

        if (chat.value[interlocuterField]) {
          await execute(
            'post',
            '/user/user/user-info',
            { id: chat.value[interlocuterField], withAgency: true, withDeveloper: true },
            {
              onSuccess: (response) => {
                let organization = null
                if (response.developer_id && ('developer' in response)) {
                  organization = response.developer
                }
                if (response.agency_id && ('agency' in response)) {
                  organization = response.agency
                }
                interlocuter.value = response
                interlocuter.value.organization = organization
              },
              onError: (err) => {
                console.error('axios request failed:', err)
              }
            }
          )
        }
      }
    }

    const fetchChat = async () => {
      if (props.chatId > 0) {
        await execute(
          'post',
          '/vidgets/messenger/get-chat',
          { id: props.chatId },
          {
            onSuccess: (response) => {
              chat.value = response
            },
            onError: (err) => {
              console.error('axios request failed:', err)
            }
          }
        )
      }
    }

    onMounted(async () => {
      await Promise.all([fetchMessages(), fetchChat()]);
    })

    watch(() => chat.value, () => {
      fetchInterlocuter()
    }, { deep: true })

    watch(() => props.chatId, () => {
      fetchChat()
      fetchMessages()
    })

    const messagesContainer = ref(null)

    const scrollToLastMessage = () => {
      nextTick(() => {
        if (messagesContainer.value) {
          messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
      })
    }

    return {
      chat,
      messages,
      interlocuter,
      isLoading,
      messagesContainer,
    }
  }
}
</script>

<style scoped>
.messages-container {
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #ccc;
  /*padding: 10px;*/
  /*margin-bottom: 10px;*/
}
</style>