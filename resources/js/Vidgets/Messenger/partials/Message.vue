<template>
  <q-chat-message
    :text="[message.text]"
    :sent="message.author_id === user.id"
    :bg-color="message.author_id === user.id ? 'white' : 'light-green-3'"
  >
    <template v-slot:name v-if="authorInfo">
      <span>{{ authorInfo.first_name }} {{ authorInfo.last_name }}</span>
      <span class="text-lowercase">, {{ authorInfo.roleLabel }}</span>
      <span v-if="('agency' in authorInfo) && authorInfo.agency.name">
        , агентство "{{ authorInfo.agency.name }}"
      </span>
      <span v-if="('developer' in authorInfo) && authorInfo.developer.name">
        , "{{ authorInfo.developer.name }}"
      </span>
    </template>
    <template v-slot:stamp v-if="message.created_at">{{ asDateTime(message.created_at) }}</template>
    <template v-slot:avatar v-if="authorInfo">
      <img
        class="q-message-avatar q-message-avatar--sent"
        :class="{'q-mr-sm': message.author_id !== user.id }"
        :src="authorInfo.photo ? `/uploads/${authorInfo.photo}` : '/img/user-nofoto.jpg'"
      >
    </template>
  </q-chat-message>
</template>

<script>
import { ref, watch } from 'vue'
import { userInfo } from '@/composables/shared-data'
import { useAxiosRequest } from '@/composables/useAxiosRequest'
import { asDateTime } from '@/helpers/formatter'

export default {
  props: {
    message: {
      type: [Object, null],
      default: null,
    }
  },
  setup (props) {
    const { user } = userInfo()

    const authorInfo = ref(null)

    const { execute } = useAxiosRequest()

    const fetchAuthor = async () => {
      await execute(
        'post',
        '/user/user/user-info',
        { id: props.message.author_id, withAgency: true, withDeveloper: true },
        {
          onSuccess: (response) => {
            authorInfo.value = response
            //console.error(authorInfo.value)
          },
          onError: (err) => {
            console.error('axios request failed:', err)
          }
        }
      )
    }

    watch (() => authorInfo, () => fetchAuthor(), { immediate: true })

    return {
      user,
      authorInfo,
      asDateTime,
    }
  }
}
</script>

<style scoped>
/*.message {
  padding: 5px;
  border-bottom: 1px solid #eee;
}*/
</style>