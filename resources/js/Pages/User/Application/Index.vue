<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Заявки">
        <template v-slot:content>
          
          <!-- New application (via selection an object) form -->
          <template v-if="user.role === 'agent' || user.role === 'manager'">
            <q-btn class="q-mt-sm" unelevated color="primary" label="Создать заявку" icon="note_add" @click="newAppDialog = true" />
            <q-dialog v-model="newAppDialog">
              <q-card>
                <q-card-section class="row items-center q-pb-none no-wrap">
                  <div class="text-h4">Укажите объект для бронирования</div>
                  <q-space />
                  <q-btn class="q-ml-md" icon="close" flat round dense v-close-popup />
                </q-card-section>
                <q-card-section>
                  <SelectObject />
                </q-card-section>
                <q-card-actions align="right">
                  <q-btn
                    color="primary"
                    rounded
                    unelevated
                    label="Создать заявку"
                    :disable="!flatIdForNewApplication"
                    @click="goToMakeApplication"
                  />
                  <q-btn
                    rounded
                    unelevated
                    label="Отмена"
                    icon="close"
                    v-close-popup
                  />
                </q-card-actions>
              </q-card>
            </q-dialog>
          </template>
          
          <GridTableToggle :defaultMode="appsGridView" />

          <div class="row q-pt-md q-col-gutter-none items-center">
            <!-- Manager selects if to show his own apllication or applications of agents -->
            <template v-if="user.role === 'manager'">
            <div :class="[showFilter.value === 'agency' ? 'col-5 col-sm-6' : 'col-10 col-sm-11']">
              <q-select class="q-gutter-sm" outlined v-model="showFilter" :options="showOptions" label="Показывать заявки" @update:model-value="onShowFilterChange" dense options-dense>
                <template v-slot:prepend>
                  <q-icon :name="showFilter.value === 'self' ? 'person' : 'group'" />
                </template>
              </q-select>
            </div>
            </template>
            <!-- Agency selection for admin -->
            <template v-if="user.role === 'admin'">
              <div class="col-5 col-sm-6">
                <q-select class="q-gutter-sm" outlined v-model="agencyFilter" :options="agencyOptions" label="Агентство" emit-value map-options dense options-dense>
                  <template v-slot:prepend>
                    <q-icon name="corporate_fare" />
                  </template>
                </q-select>
              </div>
            </template>
            <!-- Agent selection -->
            <template v-if="user.role === 'admin' || (user.role === 'manager' && showFilter.value === 'agency')">
              <div class="col-5">
                <q-select class="q-gutter-sm" outlined v-model="agentFilter" :options="agentOptions" label="Сотрудник" emit-value map-options dense options-dense>
                  <template v-slot:prepend>
                    <q-icon name="person" />
                  </template>
                </q-select>
              </div>
            </template>
            <!-- Agent selection for admin or manager -->
            <!-- Date range filter -->
            <div class="col text-right">
              <q-btn class="q-ml-sm" unelevated round icon="event" color="primary">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date
                    range
                    v-model="dateRange"
                  >
                    <div class="row q-col-gutter-none">
                      <div class="col-12 q-my-none q-py-none">
                        <q-select
                          outlined
                          v-model="dateParam"
                          :options="[{ label: 'созданные за период', value: 'created_at' }, { label: 'обновленные за период', value: 'updated_at' } ]"
                          label="Показать заявки"
                          dense
                          options-dense
                        />
                      </div>
                    </div>
                    <div class="row items-center justify-end q-gutter-sm">
                      <q-btn label="Закрыть" color="primary" flat v-close-popup />
                      <q-btn label="Сбросить" color="primary" flat @click="dateRange = {}" />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-btn>
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
                      Открыть
                    </inertia-link>
                  </q-td>
                  <q-td key="archive" :props="props">
                    <q-btn round flat color="primary" icon="archive" @click="moveToArchive(props.row.id)">
                      <q-tooltip :delay="1000" :offset="[0, 5]">Поместить в архив</q-tooltip>
                    </q-btn>
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
import { ref, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import GridTableToggle from '@/Components/Elements/GridTableToggle.vue'
import SelectObject from '@/Components/Forms/SelectObject.vue'
import useEmitter from '@/composables/use-emitter'
import { asDate, asUpdateDateTime } from '@/helpers/formatter'
import { idNameObjToOptions, userOptionList } from '@/composables/formatted-and-processed-data'
import { getApplicationFormParamsByStatus } from '@/composables/components-configurations'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    GridTableToggle,
    SelectObject
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
    },
    agencies: Array,
    agents: Array,
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

    const newAppDialog = ref(false)

    const columns = [
      { name: 'application_number', required: true, align: 'left', label: 'Номер заявки', field: 'application_number', sortable: false },
      { name: 'author', required: true, align: 'left', label: 'Автор', field: 'author', sortable: false },
      { name: 'status', required: true, align: 'left', label: 'Статус', field: 'status', sortable: false },
      { name: 'client_fio', align: 'left', label: 'ФИО клиента', field: 'client_fio', sortable: false },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
      { name: 'archive', align: 'center', label: '', field: 'archive', sortable: false },
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

    /** Range of dates */
    const dateRange = ref({})
    const dateParam = ref({ label: "созданные за период", value: 'created_at' })

    /** agencies and agents filters */
    const agencyOptions = computed(() => {
      return idNameObjToOptions(props.agencies)
    })

    const agentOptions = computed(() => { 
      return userOptionList(props.agents)
    })

    const agencyFilter = ref()

    const agentFilter = ref()

    const onShowFilterChange = () => {
      Inertia.get('/user/application/index', { show: showFilter.value.value, dateRange: dateRange.value, dateParam: dateParam.value.value, agency: agencyFilter.value, agent: agentFilter.value }, { preserveScroll: true, preserveState: true })
    }

    watch ([dateRange, dateParam, agencyFilter, agentFilter], () => {
      console.log('data range filter')
      Inertia.get('/user/application/index', { show: showFilter.value.value, dateRange: dateRange.value, dateParam: dateParam.value.value, agency: agencyFilter.value, agent: agentFilter.value }, { preserveScroll: true, preserveState: true })
    })

    /** New application form */
    const flatIdForNewApplication = ref('')

    emitter.on('select-object-flat', (payload) => {
      if ('flat_select' in payload && payload.flat_select) {
        flatIdForNewApplication.value = payload.flat_select
      }
    })

    const goToMakeApplication = () => {
      Inertia.get(`/reservation/make?flatId=${flatIdForNewApplication.value}`)
    }

    const moveToArchive = function(id) {
      Inertia.post(`/user/application/index`, { operation: 'moveToArchive', id: id })
      Inertia.on('finish', (event) => {
      })
    }

    return {
      asUpdateDateTime,
      user,
      breadcrumbs,
      goToApplication,
      appsGridView,
      newAppDialog,
      columns,
      rows,
      pagination,
      onRequest,
      showOptions,
      dateRange,
      dateParam,
      showFilter,
      onShowFilterChange,
      agencyOptions,
      agentOptions,
      agencyFilter,
      agentFilter,
      flatIdForNewApplication,
      goToMakeApplication,
      moveToArchive
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