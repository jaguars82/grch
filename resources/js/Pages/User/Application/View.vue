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
            <template v-if="statusChangesForm">
              <p>Требуемое действие:</p>
              <p>{{ statusChangesForm.operationLabel }}</p>
              <inertia-link :href="`update?id=${application.id}`">
                <q-btn :label="statusChangesForm.submitLabel" />
              </inertia-link>
            </template>
            <p class="text-h5 q-mb-xs q-mt-lg">История</p>
            <div class="q-pt-md">
              <q-table
                :rows="rows"
                :columns="columns"
                row-key="id"
                hide-bottom
              >
              </q-table>
            </div>
        </template>
      </RegularContentContainer>
      <FlatListItem class="q-ml-md q-mt-md" :flat="flat" />
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import { getApplicationFormParamsByStatus } from '@/composables/components-configurations'
import { asDateTime } from '@/helpers/formatter'
import { userInfo } from '@/composables/shared-data'

export default ({
components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    FlatListItem
  },
  props: {
    application: Array,
    applicationHistory: Array,
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

    const columns = [
      { name: 'action', required: true, align: 'left', label: 'Статус', field: 'action', sortable: true },
      { name: 'made_at', required: true, align: 'center', label: 'Дата', field: 'made_at', sortable: true },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.applicationHistory.forEach(row => {
        const processedItem = {
          id: row.id,
          action: props.statusMap[row.action],
          made_at: asDateTime(row.made_at),
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    const statusChangesForm = getApplicationFormParamsByStatus(props.application.status, user.value.role)

    return { user, breadcrumbs, createDate, updateDate, columns, rows, statusChangesForm }
  },
})
</script>
