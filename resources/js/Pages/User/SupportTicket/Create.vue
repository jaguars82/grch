<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Создание запроса в техподдержку">
        <template v-slot:content>
          <q-form
            @submit="onSubmit"
            @reset="onReset"
          >
            <div class="row q-py-sm">
              <div class="col-12">
                <q-input outlined v-model="formfields.title" label="Тема запроса" />
              </div>
              <div class="col-12">
                <q-input outlined expandable v-model="formfields.text" label="Текст сообщения" />
              </div>
            </div>
            <div class="q-mt-lg text-center">
              <q-btn label="Отправить запрос" type="submit" color="primary" unelevated />
              <q-btn label="Отмена" unelevated outlined class="q-ml-sm" @click="onCancel" />
            </div>
          </q-form>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
  },
  setup(props) {
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
        label: 'Создать запрос',
        icon: 'live_help',
        url: '/user/support-ticket/create',
        data: false,
        options: false
      },
    ])

    const loading = ref(false)

    const { user } = userInfo()

    const formfields = ref(
      {
        title: '',
        text: '',
      }
    )

    function onSubmit() {
      
      loading.value = true
      Inertia.post(`/user/support-ticket/create`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    function onCancel() {
      Inertia.visit('/user/support/index')
    }

    return { breadcrumbs, loading, user, formfields, onSubmit, onCancel }
  },
})
</script>