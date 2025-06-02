<template>
  <div
    class="rounded-borders q-mb-sm cursor-pointer"
    :class="{'bg-primary': isSelected, 'bg-white': !isSelected}"
  >
    <template v-if="interlocuter">
      <div class="flex no-wrap q-pa-sm">
        <q-avatar
          size="sm"
        >
          <img :src="interlocuter.photo ? `/uploads/${interlocuter.photo}` : '/img/user-nofoto.jpg'" />
        </q-avatar>
        <div class="q-pl-sm">
          <div>
            <span v-if="interlocuter.last_name"><strong>{{ interlocuter.last_name }}&nbsp;</strong></span>
            <span v-if="interlocuter.first_name"><strong>{{ interlocuter.first_name }}</strong></span>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAxiosRequest } from '@/composables/useAxiosRequest'
import { userInfo } from '@/composables/shared-data'

export default {
  props: {
    chat: Object,
    isSelected: {
      type: Boolean,
      default: false,
    }
  },
  setup (props) {
    const { user } = userInfo()

    const interlocuter = ref(null)

    const { execute, isLoading } = useAxiosRequest()

    const fetchInterlocuter = async () => {
      if (props.chat.type === 'private') {
        const interlocuterField = user.id === props.chat.creator_id ? 'interlocuter_id' : 'creator_id'

        if (props.chat[interlocuterField]) {
          await execute(
            'post',
            '/user/user/user-info',
            { id: props.chat[interlocuterField] },
            {
              onSuccess: (response) => {
                interlocuter.value = response
              },
              onError: (err) => {
                console.error('axios request failed:', err)
              }
            }
          )
        }
      }
    }

    onMounted(fetchInterlocuter)

    return {
      interlocuter,
    }
  }
}

</script>