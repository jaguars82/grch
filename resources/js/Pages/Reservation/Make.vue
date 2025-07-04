<template>
  <div>
    <MainLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <q-card class="q-ma-md shadow-7">
          <q-card-section>
            <p class="text-center" :class="{ 'text-h4': $q.screen.xs, 'text-h3': $q.screen.gt.xs }">Заявка на бронирование квартиры</p>
          </q-card-section>
          <q-card-section>
            <Loading v-if="loading" />
            <div v-else-if="result === 'ok'">
              <MessageScreen
                type='success'
                :textMessages="{ 
                  title: 'Заявка отправлена',
                  content: 'Дождитесь подтверждения от администратора. Статус заявки и уведомления Вы можете отслеживать в Кабинете Пользователя'
                }"
                :actions="[ 
                  {
                    id: 1,
                    icon: 'article',
                    text: 'Перейти к заявке',
                    action: goToApplication,
                    style: {
                      color: 'primary'
                    }
                  },
                  {
                    id: 2,
                    icon: 'business_center',
                    text: 'В кабинет',
                    action: goToProfile,
                    style: {
                      color: 'primary',
                      outline: true
                    }
                  }
                 ]"
              />
            </div>
            <div v-else-if="result === 'err'">
              <MessageScreen
                type='error'
                :textMessages="{ 
                  title: 'Ошибка в формировании заявки',
                  content: 'Пожалуйста, проверьте корректность введённых данных и параметров заявки'
                }"
                :actions="[ 
                  {
                    id: 1,
                    icon: 'article',
                    text: 'Назад к заявке',
                    action: goBackToApplication,
                    style: {
                      color: 'primary'
                    }
                  },
                  {
                    id: 2,
                    icon: 'close',
                    text: 'Закрыть',
                    action: closeApplication,
                    style: {
                      color: 'primary',
                      outline: true
                    }
                  }
                 ]"
              />
            </div>
            <q-form
              v-else
              @submit="onSubmit"
              @reset="onReset"
            >
            <!-- Message if the applicant already has an active reservation of this flat -->
            <q-banner v-if="anotherAppExists" rounded class="q-mx-md bg-orange text-white">
              <template v-slot:avatar>
                <q-icon name="report" color="white" />
              </template>
              <span class="text-h5"><span class="text-uppercase">Обратите внимание</span>: Вы уже создавали заявку на бронирование этого объекта. Чтобы создать новую заявку, пожалуйста, удалите предыдущую.</span>
            </q-banner>

            <div class="row q-py-sm q-col-gutter-none">
              <div class="col-sm-4 col-xs-12">
                <q-input outlined v-model="formfields.client_lastname" label="Фамилия клиента" />
              </div>
              <div class="col-sm-4 col-xs-12">
                <q-input outlined v-model="formfields.client_firstname" label="Имя клиента" />
              </div>
              <div class="col-sm-4 col-xs-12">
                <q-input outlined v-model="formfields.client_middlename" label="Отчество клиента" />
              </div>
            </div>

            <div class="row q-py-sm q-col-gutter-none">
              <div class="col-sm-6 col-xs-12">
                <q-input
                  outlined
                  mask="# (###) ###-##-##"
                  unmasked-value
                  v-model="formfields.client_phone"
                  label="Телефон клиента"
                />
              </div>
              <div class="col-sm-6 col-xs-12">
                <q-input
                  outlined
                  v-model="formfields.client_email"
                  label="Email клиента"
                  :rules="[
                    (val) =>
                      (/^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(val)) || 'Пожалуйста, введите корректный email',
                  ]"
                />
              </div>
            </div>

            <div class="row q-py-sm">
              <div class="col q-mr-md">
                <q-input outlined autogrow v-model="formfields.applicant_comment" label="Комментарий к заявке" />
              </div>
            </div>

            <div class="row q-py-sm">
              <div class="col q-mr-sm">
                <q-checkbox v-model="formfields.self_reservation" label="Самостоятельное бронирование"></q-checkbox>
              </div>
            </div>

            <div v-if="formfields.self_reservation" class="row q-col-gutter-none">
              <div class="col q-mr-md">
                <q-banner inline-actions rounded class="q-mx-md bg-orange text-white">
                  <template v-slot:avatar>
                    <q-icon name="report" color="white" />
                  </template>
                  <span class="text-h5"><span class="text-uppercase">Обратите внимание</span>: при выборе этой опции Вы осуществляете действия по бронированию объекта самостоятельно, без помощи агрегатора</span>
                </q-banner>
              </div>
            </div>

            <div v-if="formfields.self_reservation" class="row q-col-gutter-none q-mt-sm">
              <div class="col q-mr-md">
                <q-banner inline-actions rounded class="q-mx-md bg-red-2 text-red">
                  <template v-slot:avatar>
                    <q-icon name="report" color="red" />
                  </template>
                  <span class="text-h5 text-uppercase">Сделку у застройщика заводить от ГРЧ</span>
                </q-banner>
              </div>
            </div>

            <div class="q-mt-lg text-right">
              <q-btn
                :padding="$q.screen.gt.xs ? 'xs md' : 'sm'"
                unelevated
                :round="$q.screen.xs"
                :rounded="$q.screen.gt.xs"
                :label="$q.screen.sm ? 'Отправить' : $q.screen.gt.sm ? 'Отправить заявку' : ''"
                :disabled="anotherAppExists"
                type="submit"
                color="primary"
                icon="done"
              />
              <q-btn
                :label="$q.screen.gt.xs ? 'Сбросить' : ''"
                type="reset"
                color="primary"
                flat
                :padding="$q.screen.gt.xs ? 'xs md' : 'sm'"
                :round="$q.screen.xs"
                :rounded="$q.screen.gt.xs"
                class="q-ml-sm"
                icon="refresh"
              />
              <q-btn
                :label="$q.screen.gt.xs ? 'Отмена' : ''"
                color="primary"
                flat
                :padding="$q.screen.gt.xs ? 'xs md' : 'sm'"
                :round="$q.screen.xs"
                :rounded="$q.screen.gt.xs"
                class="q-ml-sm"
                @click="closeApplication"
                icon="close"
              />
            </div>
            </q-form>
          </q-card-section>
        </q-card>
        <FlatListItem class="q-mx-md" :flat="flat" />
      </template>
    </MainLayout>
  </div>
</template>

<script>

import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import MessageScreen from '@/Components/MessageScreen.vue'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    MainLayout,
    Breadcrumbs,
    Loading,
    FlatListItem,
    MessageScreen
  },
  props: {
    flat: Object,
    applicationsAmount: String,
    result: String,
    appId: String,
    anotherAppExists: Boolean
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
        label: 'Заявки',
        icon: 'real_estate_agent',
        url: '/user/application/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `Создание заявки на бронирование`,
        icon: 'note_add',
        url: `reservation/make?flatId=${props.flat.id}`,
        data: false,
        options: false
      },
    ])

    const loading = ref(false)

    const { user } = userInfo()

    const numberString = computed(function () {
      const newAmount = parseInt(props.applicationsAmount) + 1
      return `${user.value.id}-#${newAmount}`
    })

    const formfields = ref(
      {
        flat_id: props.flat.id,
        developer_id: props.flat.newbuildingComplex.developer_id,
        applicant_id: user.value.id,
        status: 1,
        client_firstname: '',
        client_lastname: '',
        client_middlename: '',
        client_phone: '',
        client_email: '',
        applicant_comment: '',
        self_reservation: false,
        is_active: true,
        application_number: numberString
      }
    )

    function onSubmit() {
      loading.value = true
      if (formfields.value.self_reservation) {
        formfields.value.status = 12
      }
      Inertia.post(`/reservation/make?flatId=${props.flat.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    const onReset = () => {
      formfields.value.client_firstname = ''
      formfields.value.client_lastname = ''
      formfields.value.client_middlename = ''
      formfields.value.client_phone = ''
      formfields.value.client_email = ''
      formfields.value.applicant_comment = ''
      formfields.value.self_reservation = false
    }

    const closeApplication = () => window.location.href = `/flat/view?id=${ props.flat.id }`

    // success screen
    const goToApplication = () => Inertia.get('/user/application/view', { id: props.appId })
    const goToProfile = () => Inertia.get('/user/profile')

    // error screen
    const goBackToApplication = () => Inertia.get('/reservation/make', { flatId: props.flat.id })

    return { breadcrumbs, loading, user, numberString, formfields, onSubmit, onReset, goToApplication, goToProfile, goBackToApplication, closeApplication }
  },
})
</script>
