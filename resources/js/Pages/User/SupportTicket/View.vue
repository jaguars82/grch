<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer
        :title="ticket.ticket_number" 
        :subtitle="ticket.title"
      >
        <template v-slot:content>
          <div class="q-py-sm q-px-md rounded-borders bg-grey-3">
            <q-chat-message
              v-for="message of messages"
              :key="message.id"
              :text="[message.text]"
              sent
              text-color="white"
              bg-color="primary"
            >
              <template v-slot:name>
                <span>{{ message.author.first_name }} {{ message.author.last_name }}</span>
                <span class="text-lowercase">, {{ message.author.roleLabel }}</span>
                <span v-if="message.author.agency_name">
                  "{{ message.author.agency_name }}"
                </span>
              </template>
              <template v-slot:stamp>{{ asDateTime(message.created_at) }}</template>
              <template v-slot:avatar>
                <img
                  class="q-message-avatar q-message-avatar--sent"
                  :src="message.author.photo ? `/uploads/${message.author.photo}` : '/img/user-nofoto.jpg'"
                >
              </template>
            </q-chat-message>
          </div>
            <q-form  @submit="onSubmit">
              <div class="row q-py-sm">
                <div class="col q-mx-md">
                  <q-input outlined autogrow v-model="formfields.text" label="Написать сообщение" />
                </div>
              </div>
              <div class="q-mt-lg text-center">
                <q-btn label="Отправить сообщение" type="submit" color="primary"/>
              </div>
            </q-form>

        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '../../../Layouts/ProfileLayout.vue'
import Breadcrumbs from '../../../Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '../../../Components/Layout/RegularContentContainer.vue'
import { asDateTime } from '../../../helpers/formatter'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer
  },
  props: {
    ticket: Array,
    messages: Array
  },
  setup(props) {
    const breadcrumbs = [
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: false
      },
      {
        id: 2,
        label: 'Кабинет пользователя',
        icon: 'business_center',
        url: '/user/profile',
        data: false,
        options: false
      },
      {
        id: 3,
        label: 'Техподдержка',
        icon: 'support_agent',
        url: '/user/support/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `Обращение ${props.ticket.ticket_number}`,
        icon: 'forum',
        url: '/user/support-ticket/view',
        data: { id: props.ticket.id },
        options: false
      },
    ]

        const loading = ref(false)

    //const { user } = userInfo()

    const formfields = ref(
      {
        text: '',
      }
    )

    function onSubmit() {
      
      loading.value = true
      Inertia.post(`/user/support-ticket/view?id=${props.ticket.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        console.log('rwrer1111')
        loading.value = false
      })
    }

    return { breadcrumbs, asDateTime, formfields, onSubmit }
  },
})
</script>