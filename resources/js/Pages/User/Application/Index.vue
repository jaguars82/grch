<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Заявки">
        <template v-slot:content>
          
          <GridTableToggle :defaultMode="appsGridView" />

          <div v-if="user.role === 'manager'" class="row q-pt-md">
            <div class="col-1 self-center">
             <q-icon size="md" :name="showFilter.value === 'self' ? 'person' : 'group'" />
            </div>
            <div class="col-11">
              <q-select class="q-gutter-sm" outlined v-model="showFilter" :options="showOptions" label="Показывать заявки" @update:model-value="onShowFilterChange" dense options-dense />
            </div>
          </div>

          <div class="q-pt-md">
            <q-table
              class="datatable no-shadow"
              bordered
              :grid="appsGridView"
              :rows="rows"
              :columns="columns"
              v-model:pagination="pagination"
              @request="onRequest"
              row-key="application_number"
            >
              <template v-slot:body="props">
                <q-tr :props="props">
                  <q-td key="application_number" :props="props">
                    <inertia-link :href="`/user/application/view?id=${props.row.id}`"><span class="text-bold">{{ props.row.application_number }}</span></inertia-link> от {{ props.row.created }}
                  </q-td>
                  <q-td key="author" :props="props">
                    <div><span class="text-bold">{{ props.row.author }},</span>
                    {{ props.row.agency }}, {{ props.row.role }}</div>
                    <div>{{ props.row.author_phone }}</div>
                  </q-td>
                  <q-td key="status" :props="props">
                    <div>{{ props.row.status }}</div>
                    <div class="text-grey-7 text-caption">Последнее обновление: {{ asUpdateDateTime(props.row.updated) }}</div>
                    <template v-if="props.row.expectedAction">
                      <template v-if="
                        user.role === 'manager' && props.row.applicant_id == user.id
                        || user.role === 'agent' && props.row.applicant_id == user.id
                        || user.role === 'developer_repres' && props.row.developer_id == user.developer_id
                        || user.role === 'admin'
                      ">
                        <q-banner inline-actions dense rounded class="q-mt-xs bg-orange-1 text-orange-9">
                          {{ props.row.expectedAction.operationLabel }}
                          <template v-slot:avatar>
                            <q-icon name="error" color="orange" />
                          </template>
                          <template v-slot:action>
                            <q-btn size="xs" flat round icon="arrow_forward" @click="goToApplication(props.row.id)" />
                          </template>
                        </q-banner>
                      </template>
                    </template>
                  </q-td>
                  <q-td key="client_fio" :props="props">
                    <div class="text-bold">{{ props.row.client_fio }}</div>
                    <div v-if="props.row.client_phone">{{ props.row.client_phone }}</div>
                  </q-td>
                  <q-td key="link" :props="props">
                    <inertia-link :href="props.row.link">
                      Подробнее
                    </inertia-link>
                  </q-td>
                </q-tr>
              </template>

              <template v-slot:item="props">
                <div class="q-pa-xs col-xs-12 col-sm-6 col-md-4">
                  <q-card>
                    <inertia-link :href="`/user/application/view?id=${props.row.id}`">
                      <q-card-section class="text-center">
                        <p>Заявка</p>
                        <p class="q-mb-xs text-h4">{{ props.row.application_number }}</p>
                        <p>{{ props.row.author }}</p>
                      </q-card-section>
                    </inertia-link>
                    <q-separator />
                  </q-card>
                </div>
              </template>

            </q-table>
          </div>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import GridTableToggle from '@/Components/Elements/GridTableToggle.vue'
import useEmitter from '@/composables/use-emitter'
import { asDate, asUpdateDateTime } from '@/helpers/formatter'
import { getApplicationFormParamsByStatus } from '@/composables/components-configurations'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    GridTableToggle
  },
  props: {
    applications: Array,
    statusMap: Array,
    totalRows: String,
    page: Number,
    psize: Number,
    show: {
      type: String,
      default: 'self'
    }
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
    ])

    const pagination = computed(() => {
      return {
        page: props.page + 1,
        rowsPerPage: props.psize,
        rowsNumber: props.totalRows
      }
    })

    const columns = [
      { name: 'application_number', required: true, align: 'left', label: 'Номер заявки', field: 'application_number', sortable: true },
      { name: 'author', required: true, align: 'left', label: 'Автор', field: 'author', sortable: true },
      { name: 'status', required: true, align: 'left', label: 'Статус', field: 'status', sortable: true },
      { name: 'client_fio', align: 'left', label: 'ФИО клиента', field: 'client_fio', sortable: true },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.applications.forEach(row => {
        const processedItem = {
          id: row.id,
          author: `${row.author.last_name} ${row.author.first_name}`,
          author_phone: row.author.phone,
          agency: row.author.agency_name,
          role: row.author.roleLabel,
          application_number: row.application_number,
          status: props.statusMap[row.status],
          client_fio: `${row.client_lastname} ${row.client_firstname} ${row.client_middlename}`,
          client_phone: row.client_phone,
          link: `/user/application/view?id=${row.id}`,
          expectedAction: getApplicationFormParamsByStatus(row.status, user.value.role),
          applicant_id: row.applicant_id,
          developer_id: row.developer_id,
          created: asDate(row.created_at),
          updated: row.updated_at,
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    const goToApplication = (applicationId) => {
      Inertia.get(`/user/application/view`, { id: applicationId })
    }

    const appsGridView = ref(false)

    const emitter = useEmitter()
    emitter.on('toggle-grid-table', (e) => appsGridView.value = e)

    const onRequest = (e) => {
      Inertia.get(`/user/application/index`, { page: e.pagination.page, psize: e.pagination.rowsPerPage, show:showFilter.value.value }, { preserveScroll: true })
    }

    /** show filter */
    const showOptions = [
      { label: 'Только мои', value: 'self' },
      { label: 'Всех сотрудников агентства', value: 'agency' },
    ]

    const selectedShowOption = computed(() => {
      const filtered = showOptions.filter(item => {
        return item.value === props.show
      })
      return filtered[0]
    })

    const showFilter = ref(selectedShowOption.value)

    const onShowFilterChange = () => {
      Inertia.post('/user/application/index', { show: showFilter.value.value }, { preserveScroll: true, preserveState: true })
    }

    return {
      asUpdateDateTime,
      user,
      breadcrumbs,
      goToApplication,
      appsGridView,
      columns,
      rows,
      pagination,
      onRequest,
      showOptions,
      showFilter,
      onShowFilterChange
    }
  },
})
</script>

<style scoped>
.datatable {
  max-width: 100% !important;
}
	
.datatable .q-table {
  max-width: 100% !important;
}
	
.datatable td {
	white-space: normal !important;
	word-wrap: normal !important;
	hyphens: manual;
}

.datatable th {
  text-align: center !important;
}

</style>