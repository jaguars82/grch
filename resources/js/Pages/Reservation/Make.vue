<template>
  <div>
    <MainLayout>
      <template v-slot:main>
        <q-card class="q-my-md shadow-7">
          <q-card-section>
            <h3 class="text-center">Заявка на бронирование квартиры</h3>
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
            <div class="row q-py-sm">
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

            <div class="row q-py-sm">
              <div class="col-sm-6 col-xs-12">
                <q-input outlined v-model="formfields.client_phone" label="Телефон клиента" />
              </div>
              <div class="col-sm-6 col-xs-12">
                <q-input outlined v-model="formfields.client_email" label="Email клиента" />
              </div>
            </div>

            <div class="row q-py-sm">
              <div class="col q-mx-md">
                <q-input outlined autogrow v-model="formfields.applicant_comment" label="Комментарий к заявке" />
              </div>
            </div>

            <div class="q-mt-lg text-center">
              <q-btn label="Отправить заявку" type="submit" color="primary"/>
              <q-btn label="Сбросить" type="reset" color="primary" flat class="q-ml-sm" />
              <q-btn label="Отмена" color="primary" flat class="q-ml-sm" @click="closeApplication" />
            </div>
            </q-form>
          </q-card-section>
        </q-card>
        <FlatListItem :flat="flat"></FlatListItem>
      </template>
    </MainLayout>
  </div>
</template>

<script>

import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Loading from "@/Components/Elements/Loading.vue"
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import MessageScreen from '@/Components/MessageScreen.vue'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    MainLayout,
    Loading,
    FlatListItem,
    MessageScreen
  },
  props: {
    flat: Object,
    applicationsAmount: String,
    result: String,
    appId: String
  },
  setup(props) {

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
        is_active: true,
        application_number: numberString
      }
    )

    function onSubmit() {
      loading.value = true
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
    }

    const closeApplication = () => window.location.href = `/flat/view?id=${ props.flat.id }`

    // success screen
    const goToApplication = () => Inertia.get('/user/application/view', { id: props.appId })
    const goToProfile = () => Inertia.get('/user/profile')

    // error screen
    const goBackToApplication = () => Inertia.get('/reservation/make', { flatId: props.flat.id })

    return { loading, user, numberString, formfields, onSubmit, onReset, goToApplication, goToProfile, goBackToApplication, closeApplication }
  },
})
</script>
