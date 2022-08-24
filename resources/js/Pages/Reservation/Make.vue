<template>
  <div>
    <MainLayout>
      <template v-slot:main>
        <q-card class="q-my-md shadow-7">
          <q-card-section>
            <h3 class="text-center">Заявка на бронирование квартиры {{ user.first_name }} {{ user.role }}  {{ result }}</h3>
          </q-card-section>
          <q-card-section>
            <div class="text-center" v-if="loading">
              <q-circular-progress
                indeterminate
                size="100px"
                color="primary"
                class="q-my-lg"
              />
            </div>
            <div v-else-if="result === 'ok'">
              заявка отправлена
            </div>
            <div v-else-if="result === 'err'">
              ошибка в заявке
            </div>
            <q-form
              v-else
              @submit="onSubmit"
              @reset="onReset"
            >

            <div class="row">
              <div class="col-sm-4 col-xs-12">
                <q-input v-model="formfields.client_lastname" label="Фамилия клиента" />
              </div>
              <div class="col-sm-4 col-xs-12">
                <q-input v-model="formfields.client_firstname" label="Имя клиента" />
              </div>
              <div class="col-sm-4 col-xs-12">
                <q-input v-model="formfields.client_middlename" label="Отчество клиента" />
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 col-xs-12">
                <q-input v-model="formfields.client_phone" label="Телефон клиента" />
              </div>
              <div class="col-sm-6 col-xs-12">
                <q-input v-model="formfields.client_email" label="Email клиента" />
              </div>
            </div>

            <div class="row">
              <div class="col q-mx-md">
                <q-input autogrow v-model="formfields.applicant_comment" label="Комментарий к заявке" />
              </div>
            </div>

            <div class="text-center">
              <q-btn label="Отправить заявку" type="submit" color="primary"/>
              <q-btn label="Сбросить" type="reset" color="primary" flat class="q-ml-sm" />
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
import MainLayout from '../../Layouts/MainLayout.vue'
import FlatListItem from '../../Components/Flat/FlatListItem.vue'
import { userInfo } from '../../helpers/shared-data'

export default ({
  components: {
    MainLayout,
    FlatListItem
  },
  props: {
    flat: Object,
    result: String
  },
  setup(props) {

    const loading = ref(false)

    const { user } = userInfo()

    const formfields = ref(
      {
        flat_id: props.flat.id,
        applicant_id: user.value.id,
        status: 1,
        client_firstname: '',
        client_lastname: '',
        client_middlename: '',
        client_phone: '',
        client_email: '',
        applicant_comment: '',
        is_active: true
      }
    )

    function onSubmit() {
      loading.value = true
      Inertia.post(`/reservation/make?flatId=${props.flat.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    const onReset = () => console.log('Cansel?')

    return { loading, user, formfields, onSubmit, onReset }
  },
})
</script>
