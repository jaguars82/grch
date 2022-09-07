<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="`Заявка ${application.application_number}`" :subtitle="`от ${createDate}`">
        <template v-slot:content>
          <p>
            Статус: <span class="text-lowercase">{{ statusMap[application.status] }}</span>
            <span> (последнее обновление {{ updateDate }})</span>
          </p>
          <div v-if="statusChangesForm">
            <Loading v-if="loading" />
            <template v-else>
              <p>Требуемое действие:</p>
              <p>{{ statusChangesForm.operationLabel }}</p>
              <!--
              <q-form
                @submit="onSubmit"
              >
                <input type="hidden" v-model="formfields.operation">
                <q-btn :label="statusChangesForm.submitLabel" type="submit" color="primary"/>
              </q-form>
              -->
              <inertia-link :href="`update?id=${application.id}`">
                <q-btn :label="statusChangesForm.submitLabel" />
              </inertia-link>
            </template>
          </div>
        </template>
      </RegularContentContainer>
      <FlatListItem class="q-ml-md q-mt-md" :flat="flat" />
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import { asDateTime } from '@/helpers/formatter'
import { userInfo } from '@/composables/shared-data'

export default ({
components: {
    ProfileLayout,
    Breadcrumbs,
    Loading,
    RegularContentContainer,
    FlatListItem
  },
  props: {
    application: Array,
    statusMap: Array,
    flat: Object
  },
  setup(props) {
    const { user } = userInfo()

    const createDate = computed( () => asDateTime(props.application.created_at))
    const updateDate = computed( () => asDateTime(props.application.updated_at))

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
        label: `Заявка ${props.application.application_number}`,
        icon: 'article',
        url: `/user/application/view?id=${props.application.id}`,
        data: false,
        options: false
      },
    ])

    // application status change form
    const loading = ref(false)

    const formConfigurationByStatus = {
      1: 
        [
          {
            role: 'admin',
            operationLabel: 'Подтвердите получение заявки от агента',
            operation: 'approve_app_by_admin',
            submitLabel: 'Подтвердить'
          }
        ],
      2:
        []
    } 

    const formParams = formConfigurationByStatus[props.application.status].filter((el) => {
      return el.role === user.value.role
    })

    const statusChangesForm = formParams.length > 0 ? formParams[0] : false

    const formfields = ref(
      {
        operation: statusChangesForm ? statusChangesForm.operation : '',
      }
    )

    function onSubmit() {
      loading.value = true
      Inertia.post(`/user/application/view?id=${props.application.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }
    
    return { user, breadcrumbs, createDate, updateDate, loading, statusChangesForm, formfields, onSubmit }
  },
})
</script>
