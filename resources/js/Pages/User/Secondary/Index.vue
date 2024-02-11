<template>
    <ProfileLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <RegularContentContainer title="Вторичная недвижимость">
          <template v-slot:content>
            <div v-if="user.role !== 'admin'" class="q-mt-md">
              <q-btn color="primary" unelevated label="Создать объявление" icon="post_add" @click="createAdd" />
            </div>
            <div class="row q-mt-md" v-if="user.role === 'admin' || user.role === 'manager'">
              <div class="col-1 gt-xs">
                <q-icon :color="filters.agency === null && filters.agent === null && filters.category === null ? 'grey-5' : 'orange'" size="md" name="filter_alt"></q-icon>
              </div>
              <div class="col-10">
                <div class="row q-col-gutter-none">
                  <div v-if="user.role === 'admin'" class="col-12 col-sm-6 col-md-4" :class="{ 'q-pr-none': $q.screen.gt.xs }">
                    <q-select class="q-gutter-sm" outlined clearable v-model="filters.agency" :options="agencyOptions" label="Агентство" @update:model-value="onAgencySelect" dense options-dense />
                  </div>
                  <div class="col-12 col-sm-6" :class="{ 'q-pr-none': $q.screen.gt.sm, 'col-md-4': user.role === 'admin'  }">
                    <q-select class="q-gutter-sm" outlined clearable v-model="filters.agent" :options="agentOptions" label="Сотрудник" dense options-dense />
                  </div>
                  <div class="col-12" :class="user.role === 'admin' ? 'col-md-4' : 'col-sm-6'">
                    <q-select class="q-gutter-sm" outlined clearable v-model="filters.category" :options="categoryOptions" label="Категория" dense options-dense />
                  </div>
                </div>
              </div>
              <div class="col-1">
                <q-btn :color="filters.agency === null && filters.agent === null && filters.category === null ? 'grey-5' : 'primary'" unelevated round icon="filter_alt_off" @click="filterReset">
                  <q-tooltip>
                    Сбросить фильтр
                  </q-tooltip>
                </q-btn>
              </div>
            </div>
            <div class="q-pt-md">
              <q-table
                class="no-shadow datatable"
                bordered
                :rows="rows"
                :columns="columns"
                v-model:pagination="pagination"
                row-key="id"
                @request="onRequest"
              >
                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td key="id" :props="props">
                      {{ props.row.id }}
                    </q-td>
                    <q-td key="id" :props="props">
                      <template v-for="room in props.row.objects">
                        <span class="text-strong">{{ room.title }}</span>
                        <template v-if="room.location.replaceAll(' ', '') !== ''">
                          <br />
                          <span>{{ room.location }}</span>
                        </template>
                        <template v-if="room.price">
                          <br />
                          <span>{{ room.price }}</span>
                        </template>
                      </template>
                    </q-td>
                    <q-td key="deal_type" :props="props">
                      <span v-if="props.row.deal_type === 1">продажа</span>
                      <span v-if="props.row.deal_type === 2">аренда</span>
                    </q-td>
                    <q-td key="create_info" :props="props">
                      {{ props.row.create_info }}
                    </q-td>
                    <q-td key="source" :props="props">
                      <q-icon v-if="props.row.source === 1" name="share" />
                      <q-icon v-if="props.row.source === 2" name="edit_square" />
                    </q-td>
                    <q-td key="delete" :props="props">
                      <template v-if="props.row.source === 2">
                        <q-btn icon="delete" @click="openDeleteForm(props.row.id)"/>
                      </template>
                    </q-td>
                    <q-td key="status" :props="props">
                      <template v-if="props.row.status">
                        <q-chip color="primary" class="text-white" v-for="status of props.row.status">
                          {{ status.type.name }}
                          <q-icon class="q-pl-xs cursor-pointer" name="close" @click="unsetStatus(props.row.id, status.id)">
                            <q-tooltip>
                              Удалить статус
                            </q-tooltip>
                          </q-icon>
                        </q-chip>
                      </template>
                      <template v-else>
                        <q-btn unelevated size="xs" label="Установить статус">
                          <q-menu>
                              <div class="column q-pa-md status-menu">
                                <q-select
                                  outlined
                                  dense
                                  options-dense
                                  :options="statusOptions"
                                  v-model="statusLabelForm.status"
                                >
                                </q-select>
                                <q-checkbox v-model="statusLabelForm.statusTermFlag" label="Установить срок действия" />
                                <q-date
                                  v-if="statusLabelForm.statusTermFlag"
                                  class="q-mb-sm no-shadow"
                                  bordered
                                  mask="YYYY-MM-DD"
                                  v-model="statusLabelForm.date"
                                  :options="dateOptions"
                                  minimal
                                />
                                <q-btn unelevated color="primary" size="md" label="Сохранить статус" :disable="canSaveAddStatus" @click="saveAddStatus(props.row.id)" v-close-popup />
                              </div>
                          </q-menu>
                        </q-btn>
                      </template>
                    </q-td>
                    
                    <!--<q-td key="link" :props="props">
                      <inertia-link :href="props.row.link">
                        Подробнее
                      </inertia-link>
                    </q-td>-->
                  </q-tr>
                </template>
              </q-table>
            </div>
            <q-dialog v-model="deleteFormDialog">
              <q-card>
                <q-card-section class="row items-center q-pb-none">
                  <div class="text-h6">Удаление объявления #{{ addToDeleteId }}</div>
                  <q-space />
                  <q-btn icon="close" flat round dense @click="closeDeleteDialog" />
                </q-card-section>
                <q-card-section>
                  Вы уверены, что хотите удалить обявление?
                </q-card-section>
                <q-card-actions align="right">
                  <q-btn flat @click="deleteAdd">Удалить</q-btn>
                  <q-btn flat @click="closeDeleteDialog">Отмена</q-btn>
                </q-card-actions>
              </q-card>
            </q-dialog>
          </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { date } from 'quasar'
import { asDateTime, asNumberString, asFloor, asArea, asCurrency, asPricePerArea } from '@/helpers/formatter'
import { secondaryCategoryOptionList, idNameObjToOptions, userOptionList } from '@/composables/formatted-and-processed-data'
import { userInfo } from '@/composables/shared-data'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import GridTableToggle from '@/Components/Elements/GridTableToggle.vue'
import useEmitter from '@/composables/use-emitter'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    GridTableToggle
  },
  props: {
    user: Object,
    advertisements: Array,
    totalRows: String,
    page: Number,
    psize: Number,
    labelTypes: Array,
    agencies: {
      type: Object,
      default: {}
    },
    agents: {
      type: Array,
      default: []
    },
    secondaryCategories: {
      type: Object,
      default: {}
    },
    filter: Array
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
        label: 'Вторичная недвижимость',
        icon: 'home_work',
        url: '/user/secondary/index',
        data: false,
        options: false
      },
    ])

    const loading = ref(false)

    const pagination = computed(() => {
      return {
        page: props.page + 1,
        rowsPerPage: props.psize,
        rowsNumber: props.totalRows
      }
    })

    const columns = [
      { name: 'id', required: true, align: 'left', label: 'id', field: 'id', sortable: true },
      { name: 'objects', required: true, align: 'left', label: 'Объекты', field: 'object', sortable: false },      
      { name: 'deal_type', required: true, align: 'center', label: 'Тип сделки', field: 'deal_type', sortable: true },
      { name: 'create_info', required: true, align: 'left', label: 'Создано', field: 'create_info', sortable: false },
      { name: 'source', align: 'center', label: '', field: 'source', sortable: false },
      { name: 'delete', align: 'center', label: '', field: 'delete', sortable: false },
      { name: 'status', align: 'center', label: 'Статус', field: 'status', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.advertisements.forEach(row => {

        const rooms = []
        row.rooms.forEach(room => {
          const category = room.category_DB ? room.category_DB.name : room.category_string ? room.category_string : ''

          const isFlat = room.category_DB && room.category_DB.name.toLowerCase() == 'квартира' ? true : room.category_string && room.category_string.toLowerCase() == 'квартира' ? true : false

          const roomTitle =  `${asNumberString(room.rooms)}комнатная`

          const locationInfo = JSON.parse(room.location_info)
          const regionDistrict = room.region_district_DB ? room.region_district_DB.name : locationInfo && locationInfo.district ? locationInfo.district : ''
          const city = room.city_DB ? room.city_DB.name : locationInfo && locationInfo.locality_name ? locationInfo.locality_name : ''
          const cityDistrict = room.district_DB ? room.district_DB.name : locationInfo && locationInfo.non_admin_sub_locality_name ? locationInfo.non_admin_sub_locality_name : locationInfo && locationInfo.sub_locality_name ? locationInfo.sub_locality_name : ''
          const streetHouse = room.address ? room.address : locationInfo && locationInfo.address ? locationInfo.address : ''

          rooms.push({ title: isFlat ? `${roomTitle} ${category}` : category, location: `${regionDistrict} ${city} ${cityDistrict} ${streetHouse}`, price: asCurrency(room.price) })
        })

        const processedItem = {
          id: `${row.id}`,
          objects: rooms,
          deal_type: row.deal_type,
          create_info: asDateTime(row.creation_date),
          source: row.creation_type,
          delete: `/user/secondary/delete?id=${row.id}`,
          status: row.statusLabels.length ? row.statusLabels : null
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    /*const appsGridView = ref(false)

    const emitter = useEmitter()
    emitter.on('toggle-grid-table', (e) => appsGridView.value = e)*/

    const onRequest = (e) => {
      Inertia.get(`/user/secondary/index`, { page: e.pagination.page, psize: e.pagination.rowsPerPage, agency: filters.value.agency !== null ? filters.value.agency.value : '', agent: filters.value.agent !== null ? filters.value.agent.value : '', category: filters.value.category !== null ? filters.value.category.value : '', }, { preserveScroll: true })
    }

    const statusOptions = computed(() => {
      const options = []
      props.labelTypes.forEach(labelType => {
        options.push({ label: labelType.name, value:labelType.id })
      })
      return options
    })

    function dateOptions(d) {
      return d >= date.formatDate(Date.now(), 'YYYY/MM/DD')
    }

    const statusLabelForm = ref({
      status: '',
      statusTermFlag: false,
      date: null
    })

    const clearStatusLabelForm = () => {
      statusLabelForm.value = {
        status: '',
        statusTermFlag: false,
        date: null
      }
    }
    
    const canSaveAddStatus = computed(() => {
      let result = false
      if (!statusLabelForm.value.status) result = true
      if (statusLabelForm.value.statusTermFlag && !statusLabelForm.value.date) result = true
      return result
    })

    const saveAddStatus = (addId) => {
      const fields = {
        operation: 'setStatus',
        secondary_advertisement_id: addId,
        label_type_id: statusLabelForm.value.status.value,
        has_expiration_date: statusLabelForm.value.statusTermFlag,
        expires_at: statusLabelForm.value.date
      }
      Inertia.post('/user/secondary/index', fields, { preserveScroll: true })
      Inertia.on('finish', (e) => {
        clearStatusLabelForm()
      })
    }

    const unsetStatus = (addId, statusId) => {
      const fields = {
        operation: 'unsetStatus',
        secondary_advertisement_id: addId,
        status_label_id: statusId
      }
      Inertia.post('/user/secondary/index', fields, { preserveScroll: true })
    }

    const deleteFormDialog = ref(false)
    const addToDeleteId = ref(null)

    const openDeleteForm = (addId) => {
      addToDeleteId.value = addId
      deleteFormDialog.value = true
      console.log(deleteFormDialog.value)
    }

    const closeDeleteDialog = () => {
      addToDeleteId.value = null
      deleteFormDialog.value = false
    }

    const deleteAdd = () => {
      const fields = {
        operation: 'deleteAdd',
        id: addToDeleteId.value
      }
      closeDeleteDialog()
      Inertia.post('/user/secondary/index', fields)
    }

    const createAdd = () => {
      Inertia.get('/user/secondary/create')
    }

    const agencyOptions = computed(() => {
      return idNameObjToOptions(props.agencies)
    })
    const agentOptions = computed(() => { 
      return userOptionList(props.agents)
    })
    const categoryOptions = computed(() => {
      return secondaryCategoryOptionList(props.secondaryCategories)
    })

    const selectedAgency = computed(() => {
      let result = null
      if (props.filter.agency) {
        const filtered = agencyOptions.value.filter(item => {
          return item.value == props.filter.agency
        })
        result = filtered[0]
      }
      return result
    })

    const selectedAgent = computed(() => {
      let result = null
      if (props.filter.agent) {
        const filtered = agentOptions.value.filter(item => {
          return item.value == props.filter.agent
        })
        result = filtered[0]
      }
      return result
    })

    const selectedCategory = computed(() => {
      let result = null
      if (props.filter.category) {
        const filtered = categoryOptions.value.filter(item => {
          return item.value == props.filter.category
        })
        result = filtered[0]
      }
      return result
    })

    const filters = ref({
      agency: selectedAgency.value,
      agent: selectedAgent.value,
      category: selectedCategory.value,
    })

    const onAgencySelect = () => {
      filters.value.agent = null
    }

    watch(filters.value, () => {
      const fields = {
        operation: 'filterAdds',
        agency: filters.value.agency !== null ? filters.value.agency.value : null,
        agent: filters.value.agent !== null ? filters.value.agent.value : null,
        category: filters.value.category !== null ? filters.value.category.value : null,
      }
      Inertia.post('/user/secondary/index', fields, { preserveScroll: true, preserveState: true })
    })

    const filterReset = () => {
      filters.value.agency = null
      filters.value.agent = null
      filters.value.category = null
    }

    return { 
      user,
      breadcrumbs,
      pagination,
      columns,
      statusOptions,
      dateOptions,
      statusLabelForm,
      canSaveAddStatus,
      createAdd,
      /*appsGridView,*/
      rows,
      saveAddStatus,
      unsetStatus,
      openDeleteForm,
      deleteAdd,
      closeDeleteDialog,
      deleteFormDialog,
      addToDeleteId,
      onRequest,
      agencyOptions,
      agentOptions,
      categoryOptions,
      filters,
      onAgencySelect,
      filterReset
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

.status-menu {
  width: 322px;
  min-width: 322px !important;
}
</style>