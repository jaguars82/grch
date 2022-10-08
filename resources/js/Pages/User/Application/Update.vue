<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="statusChangesForm.formCaption">
        <template v-slot:content>
          <Loading v-if="loading" />
          <div v-else>
            <div class="q-my-lg" v-html="statusChangesForm.formContent"></div>
            <q-form
              @submit="onSubmit"
            >
              <input type="hidden" v-model="formfields.operation">
                <div
                  v-if="formfields.operation === 'approve_reservation_by_developer' || formfields.operation === 'approve_reservation_from_developer_by_admin' && application.status == 2"
                  class="q-pa-md"                
                >
                  <div class="row q-py-sm">
                    <div class="col-sm-4 col-xs-12">
                      <q-input outlined v-model="formfields.manager_lastname" label="Фамилия менеджера" />
                    </div>
                    <div class="col-sm-4 col-xs-12">
                      <q-input outlined v-model="formfields.manager_firstname" label="Имя менеджера" />
                    </div>
                    <div class="col-sm-4 col-xs-12">
                      <q-input outlined v-model="formfields.manager_middlename" label="Отчество менеджера" />
                    </div>
                  </div>

                  <div class="row q-py-sm">
                    <div class="col-sm-6 col-xs-12">
                      <q-input outlined v-model="formfields.manager_phone" label="Телефон менеджера" />
                    </div>
                    <div class="col-sm-6 col-xs-12">
                      <q-input outlined v-model="formfields.manager_email" label="Email менеджера" />
                    </div>
                  </div>

                  <div class="row q-py-sm">
                    <div class="col q-mx-md">
                      <q-input outlined autogrow v-model="formfields.reservation_conditions" label="Условия бронирования" />
                    </div>
                  </div>

                </div>

                <div
                  v-if="formfields.operation === 'report_success_deal_by_agent' || formfields.operation === 'report_success_deal_by_manager'"
                  class="q-pa-md"
                >
                  <q-file
                    v-model="formfields.deal_success_docs"
                    label="Прикрепите документы о подтверждении сделки"
                    outlined
                    multiple
                  />
                </div>
              <div class="text-right">
                <q-btn unelevated :label="statusChangesForm.submitLabel" type="submit" color="primary"/>
              </div>
            </q-form>
          </div>
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
import Loading from "@/Components/Elements/Loading.vue"
import { getApplicationFormParamsByStatus } from '@/composables/components-configurations'
import { userInfo } from '@/composables/shared-data'
 
export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    Loading
  },
  props: {
    application: Array,
    statusMap: Array,
  },
  setup (props) {
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
        label: `Изменение статуса заявки ${props.application.application_number}`,
        icon: 'edit_square',
        url: `/user/application/update?id=${props.application.id}`,
        data: false,
        options: false
      },
    ])

    const { user } = userInfo()

    const loading = ref(false)

    const statusChangesForm = getApplicationFormParamsByStatus(props.application.status, user.value.role)

    const formfields = ref(
      {
        operation: statusChangesForm ? statusChangesForm.operation : '',
        manager_firstname: '',
        manager_lastname: '',
        manager_middlename: '',
        manager_phone: '',
        manager_email: '',
        reservation_conditions: '',
        deal_success_docs: null
      }
    )
    
    function onSubmit() {
      loading.value = true
      Inertia.post(`/user/application/view?id=${props.application.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    return { breadcrumbs, loading, formfields, statusChangesForm, onSubmit }
  }
}

</script>