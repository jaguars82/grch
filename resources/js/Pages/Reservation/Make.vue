<template>
  <div>
    <MainLayout>
      <template v-slot:main>
        <q-card class="q-my-md shadow-7">
          <q-card-section>
            <h3 class="text-center">Заявка на бронирование квартиры {{ user.first_name }} {{ user.role }}</h3>
          </q-card-section>
          <q-card-section>
            <q-form
              @submit="onSubmit"
              @reset="onReset"
            >

            <div class="row">
              <div class="col q-mx-md">
                <q-input v-model="formfields.client_lastname" label="Фамилия клиента" />
              </div>
              <div class="col q-mx-md">
                <q-input v-model="formfields.client_firstname" label="Имя клиента" />
              </div>
              <div class="col q-mx-md">
                <q-input v-model="formfields.client_middlename" label="Отчество клиента" />
              </div>
            </div>

            <div class="row">
              <div class="col q-mx-md">
                <q-input v-model="formfields.client_phone" label="Телефон клиента" />
              </div>
              <div class="col q-mx-md">
                <q-input v-model="formfields.client_email" label="Email клиента" />
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
    flat: Object
  },
  setup(props) {

    const { user } = userInfo()

    const formfields = ref(
      {
        flat_id: props.flat.id,
        applicant_id: user.value.id,
        client_firstname: '',
        client_lastname: '',
        client_middlename: '',
        client_phone: '',
        client_email: ''
      }
    )

    function onSubmit() {
      Inertia.post(`/reservation/make?flatId=${props.flat.id}`, formfields.value)
    }
    //const onSubmit = () => console.log(formfields.value)
    const onReset = () => console.log('Cansel?')

    return { user, formfields, onSubmit, onReset }
  },
})
</script>
