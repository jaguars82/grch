<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: $q.platform.is.mobile ? false : true } }">
    
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <div class="full-width row justify-start items-center q-px-md">
        <h3 class="q-mb-sm">Предложение #{{ advertisement.id }}</h3>
        <div v-if="advertisement.statusLabels.length" class="row q-gutter-sm justify-end" style="margin-left: auto">
          <StatusLabel v-for="label in advertisement.statusLabels" :key="label.id" :label="label" />
        </div>
      </div>
      
      <SecondaryRoomViewItem
        v-for="room of advertisement.secondary_room"
        :key="room.id"
        :room="room"
        :created="advertisement.creation_date"
        :agency="advertisement.agency"
        :author="{
            db: advertisement.author_DB ? advertisement.author_DB : null,
            info: advertisement.author_info
        }"
      /> 

      <!--<template v-if="Object.keys(advertisement.author_DB).length">        
        <Messenger
          :userId="user.id"
          :userInfo="user"
          mode="url"
          :urlParams="['id']"
          :isUserUrlAdmin="user.id === advertisement.author_DB.id ? true : false"
          :newInterlocuter="user.id !== advertisement.author_DB.id ? { ...advertisement.author_DB, organization: advertisement.agency } : null"
          :urlAdminId="advertisement.author_DB.id"
          :chatDetails='{
            title: `объявление #${advertisement.id} в разделе "Вторичка"`,
          }'
        />
      </template>-->

      <!-- Messenger Floating Button -->
      <q-page-sticky position="bottom-right" :offset="[18, 80]">
        <q-btn
          fab
          class="open-chat-button"
          v-if="Object.keys(advertisement.author_DB).length"
          icon="chat"
          color="orange"
          @click="messengerDialog = true"
        >
          <q-tooltip anchor="top middle" self="center middle">Открыть чаты</q-tooltip>
        </q-btn>
      </q-page-sticky>

      <!-- Messenger Dialog -->
      <q-dialog
        v-model="messengerDialog"
        position="right"
        full-width
        full-height
      >
        <q-card class="q-dialog-plugin shadow-24" style="width: 100%; max-width: 420px;">
          <q-bar>
            <div class="text-bold text-grey-7">Мессенджер ГРЧ</div>
            <q-space />
            <q-btn dense flat round icon="close" @click="messengerDialog = false" />
          </q-bar>
          <Messenger
            :userId="user.id"
            :userInfo="user"
            mode="url"
            :urlParams="['id']"
            :isUserUrlAdmin="user.id === advertisement.author_DB.id"
            :newInterlocuter="user.id !== advertisement.author_DB.id ? { ...advertisement.author_DB, organization: advertisement.agency } : null"
            :urlAdminId="advertisement.author_DB.id"
            :chatDetails='{
              title: `объявление #${advertisement.id} в разделе "Вторичка"` }'
          />
        </q-card>
      </q-dialog>
    </template>

    <!-- Right Drawer -->
    <template v-slot:right-drawer>
      <div class="q-pa-md">
        <div class="column">
          <p class="q-mb-md text-h4 text-center">Контакты</p>
          <!--<q-avatar v-if="advAuthor.photo" size="100px" class="q-mr-sm">-->
            <q-img fit="scale-down" class="self-center rounded-borders" height="200px" :src="advAuthor.photo ? advAuthor.photo : '/img/blank-person.svg'" />
          <!--</q-avatar>-->
          <div v-if="advAuthor.fullName" class="q-mt-md" :class="{ 'q-mb-md': !advertisement.agency }">
            <span class="text-h4">
              {{ advAuthor.fullName }}
            </span>
          </div>
          <div v-if="advertisement.agency" class="q-mb-md">
            <span class="text-h5 text-grey-7">
              агентство <b>"{{ advertisement.agency.name }}"</b>
            </span>
          </div>
          <div v-if="advAuthor.phone">
            <q-icon class="q-pr-xs" name="phone_enabled" />
            <span>
              {{ advAuthor.phone }}
            </span>
          </div>
          <div v-if="advAuthor.email">
            <a :href="`mailto:${advAuthor.email}`">
              <q-icon class="q-pr-xs" name="mail" />
              <span>
                {{ advAuthor.email }}
              </span>
            </a>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
  
<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { userInfo } from '@/composables/shared-data'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import StatusLabel from '@/Components/Elements/StatusLabel'
import SecondaryRoomViewItem from "@/Components/SecondaryRoom/SecondaryRoomViewItem.vue"
import Messenger from "@/Vidgets/Messenger/Messenger.vue"
  
export default {
  props: {
    advertisement: {
      type: Object,
      default: []
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading, StatusLabel, SecondaryRoomViewItem, Messenger
  },
  setup(props) {

    const { user } = userInfo()

    const breadcrumbs = ref([
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
        label: 'Вторичная продажа',
        icon: 'home_work',
        url: '/secondary',
        data: false,
        options: false
      },
      {
        id: 3,
        label: `Предложение #${props.advertisement.id}`,
        icon: 'newspaper',
        url: `/secondary/view?id=${props.advertisement.id}`,
        data: false,
        options: false
      },
    ])

    const messengerDialog = ref(false)

    const advAuthor = computed(() => {
      let fullName = ''
      let photo = ''
      let phone = ''
      let email = ''
      if (props.advertisement.author_DB) {
        const lastName = props.advertisement.author_DB.last_name ? `${props.advertisement.author_DB.last_name} ` : ''
        const firstName = props.advertisement.author_DB.first_name ? props.advertisement.author_DB.first_name : ''
        const middleName = props.advertisement.author_DB.middle_name ? ` ${props.advertisement.author_DB.middle_name}` : ''
        fullName = `${lastName}${firstName}${middleName}`
        photo = props.advertisement.author_DB.photo ? `/uploads/${props.advertisement.author_DB.photo}` : ''
        phone = props.advertisement.author_DB.phone ? props.advertisement.author_DB.phone : null
        email = props.advertisement.author_DB.email ? props.advertisement.author_DB.email : null
      } else {
        const authorParsed = JSON.parse(props.advertisement.author_info)
        fullName = authorParsed.name ? authorParsed.name : 'Имя не указано'
        photo = authorParsed.photo ? authorParsed.photo : null
        phone = authorParsed.phones ? authorParsed.phones.join(', ') : null
        email = authorParsed.email ? authorParsed.email : null
      }
      return { fullName, photo, phone, email }
    })

    const goBack = function (page) {
      //Inertia.get('/secondary', { page: page })
    }

    return { user, breadcrumbs, advAuthor, messengerDialog, goBack }
  }
}
</script>