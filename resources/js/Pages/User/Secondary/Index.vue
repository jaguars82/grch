<template>
    <ProfileLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <RegularContentContainer title="Вторичная недвижимость">
          <template v-slot:content>
            <!-- Create new adv button -->
            <div v-if="user.role !== 'admin'" class="q-mt-md">
              <q-btn color="primary" unelevated label="Создать объявление" icon="post_add" @click="createAdd" />
            </div>

            <!-- Filers -->
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

            <!-- Data table -->
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
                      <q-icon color="grey-7" v-if="props.row.source === 1" name="share">
                        <q-tooltip anchor="top middle" self="bottom middle">
                          Размещено из внешнего источника (фида)
                        </q-tooltip>
                      </q-icon>
                      <q-icon color="grey-7" v-if="props.row.source === 2" name="edit_square">
                        <q-tooltip anchor="top middle" self="bottom middle">
                          Размещено вручную
                        </q-tooltip>
                      </q-icon>
                    </q-td>
                    <q-td key="agent_fee" class="text-center">
                      <!-- Agent's fee -->
                      <template v-if="user.role === 'agent' || user.role === 'manager' || user.role === 'admin'">
                        <template v-if="props.row.agent_fees.length">
                          <div class="row no-wrap" v-for="fee in props.row.agent_fees">
                            {{ fee.fee_amount }}
                            <q-btn flat class="q-ml-xs" size="xs" round dense color="negative" icon="close" @click="unsetFee(fee.id)">
                              <q-tooltip anchor="top middle" self="bottom middle">
                                Удалить комиссию
                              </q-tooltip>
                            </q-btn>
                          </div>
                        </template>
                        <template v-else>
                          <q-btn unelevated size="xs" icon="percent" @mouseenter="initTooltip(props.row.id)">
                            <q-tooltip v-model="showAddFeeTooltip[props.row.id]" anchor="top middle" self="bottom middle">
                              Добавить комиссию
                            </q-tooltip>
                            <q-menu anchor="bottom left" self="top left" @beforeShow="showAddFeeTooltip[props.row.id] = false">
                              <div class="column q-pa-md options-menu">
                                <q-input
                                  outlined
                                  dense
                                  label="Укажите размер комиссии"
                                  v-model="feeForm.fee_amount"
                                  @keypress="onlyNumbersWithDot"
                                />
                                <q-btn unelevated color="primary" size="md" label="Установить комиссию" :disable="!canSaveFee" @click="saveFee(props.row.id)" v-close-popup />
                              </div>
                            </q-menu>
                          </q-btn>
                        </template>
                      </template>
                    </q-td>
                    <q-td key="delete" :props="props">
                      <template v-if="props.row.source === 2">
                        <q-btn icon="delete" size="sm" flat round color="negative" @click="openDeleteForm(props.row.id)">
                          <q-tooltip anchor="top middle" self="center middle">
                            Удалить объявление
                          </q-tooltip>
                        </q-btn>
                      </template>
                    </q-td>
                    <q-td key="status" :props="props">
                      <template v-if="props.row.status">
                        <q-chip size="sm" color="primary" class="q-pl-none text-white" v-for="status of props.row.status">
                          <q-badge v-if="status.has_expiration_date" floating rounded class="q-pa-none" color="orange">
                            <q-icon name="schedule" color="white"/>
                            <q-tooltip anchor="top left" self="center middle">
                              Установлен до {{ asDate(status.expires_at) }}
                            </q-tooltip>
                          </q-badge>
                          <!--<q-icon class="q-pr-xs cursor-pointer" name="close" @click="unsetStatus(props.row.id, status.id)">-->
                          <q-btn size="xs" dense unelevated round class="q-mr-xs q-pa-none" color="negative" icon="close" @click="unsetStatus(props.row.id, status.id)">
                            <q-tooltip anchor="top middle" self="bottom middle">
                              Удалить статус
                            </q-tooltip>
                          </q-btn>
                          {{ status.type.name }}
                        </q-chip>
                      </template>
                      <template v-else>
                        <q-btn unelevated size="xs" label="Установить статус">
                          <q-menu>
                            <div class="column q-pa-md options-menu">
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
                              <q-btn unelevated color="primary" size="md" label="Сохранить статус" :disable="cantSaveAddStatus" @click="saveAddStatus(props.row.id)" v-close-popup />
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
import { ref, reactive, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { date } from 'quasar'
import { asDate, asDateTime, asNumberString, asFloor, asArea, asCurrency, asPricePerArea } from '@/helpers/formatter'
import { secondaryCategoryOptionList, idNameObjToOptions, userOptionList } from '@/composables/formatted-and-processed-data'
import { userInfo } from '@/composables/shared-data'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import GridTableToggle from '@/Components/Elements/GridTableToggle.vue'
import { useInputValidation } from '@/composables/useInputValidation'
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
    filter: Object
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

    const pagination = ref({
      page: props.page + 1,
      rowsPerPage: props.psize,
      rowsNumber: props.totalRows
    })

    watch(() => props.page, (newPage) => {
      pagination.value.page = newPage + 1
    })

    watch(() => props.psize, (newPsize) => {
      pagination.value.rowsPerPage = newPsize
    })

    watch(() => props.totalRows, (newTotalRows) => {
      pagination.value.rowsNumber = newTotalRows
    })

    const columns = [
      { name: 'id', required: true, align: 'left', label: 'id', field: 'id', sortable: true },
      { name: 'objects', required: true, align: 'left', label: 'Объекты', field: 'object', sortable: false },      
      { name: 'deal_type', required: true, align: 'center', label: 'Тип сделки', field: 'deal_type', sortable: true },
      { name: 'create_info', required: true, align: 'left', label: 'Создано', field: 'create_info', sortable: false },
      { name: 'source', align: 'center', label: '', field: 'source', sortable: false },
      { name: 'fee', align: 'center', label: 'Комиссия', field: 'fee', sortable: false },
      { name: 'delete', align: 'center', label: '', field: 'delete', sortable: false },
      { name: 'status', align: 'center', label: 'Статус', field: 'status', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.advertisements.forEach(row => {

        const rooms = []
        const fees = []
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

          if (room.agent_fee.length) {
            room.agent_fee.forEach(fee => {
              fees.push({id: fee.id, fee_type: fee.fee_type, fee_amount: fee.fee_amount, fee_percent: fee.fee_percent})
            })
          } 
        })

        const processedItem = {
          id: `${row.id}`,
          objects: rooms,
          agent_fees: fees,
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

    /** Agent's fee related */
    const showAddFeeTooltip = reactive({})

    const initTooltip = (id) => {
      if (!showAddFeeTooltip.hasOwnProperty(id)) {
        showAddFeeTooltip[id] = false;
      }
    }

    const { onlyNumbersWithDot } = useInputValidation()

    const feeForm = ref({
      fee_amount: null,
    })

    const clearFeeForm = () => {
      feeForm.value = {
        fee_amount: null,
      }
    }
        
    const canSaveFee = computed(() => {
      let result = true
      if (!feeForm.value.fee_amount) result = false
      return result
    })

    const saveFee = (advId) => {
      const fields = {
        operation: 'setAgentFee',
        secondary_advertisement_id: advId,
        fee_amount: feeForm.value.fee_amount,
        agency: filters.value.agency !== null ? filters.value.agency.value : null,
        agent: filters.value.agent !== null ? filters.value.agent.value : null,
        category: filters.value.category !== null ? filters.value.category.value : null,
      }
      Inertia.post('/user/secondary/index', fields, { preserveScroll: true })
      Inertia.on('finish', (e) => {
        clearFeeForm()
      })
    }

    const unsetFee = (feeId) => {
        const fields = {
        operation: 'unsetAgentFee',
        fee_id: feeId,
        agency: filters.value.agency !== null ? filters.value.agency.value : null,
        agent: filters.value.agent !== null ? filters.value.agent.value : null,
        category: filters.value.category !== null ? filters.value.category.value : null,
      }
      Inertia.post('/user/secondary/index', fields, { preserveScroll: true })
    }

    /** Status label related */
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
    
    const cantSaveAddStatus = computed(() => {
      let result = false
      if (!statusLabelForm.value.status) result = true
      if (statusLabelForm.value.statusTermFlag && !statusLabelForm.value.date) result = true
      return result
    })

    const saveAddStatus = (advId) => {
      const fields = {
        operation: 'setStatus',
        secondary_advertisement_id: advId,
        label_type_id: statusLabelForm.value.status.value,
        has_expiration_date: statusLabelForm.value.statusTermFlag,
        expires_at: statusLabelForm.value.date,
        agency: filters.value.agency !== null ? filters.value.agency.value : null,
        agent: filters.value.agent !== null ? filters.value.agent.value : null,
        category: filters.value.category !== null ? filters.value.category.value : null,
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
        status_label_id: statusId,
        agency: filters.value.agency !== null ? filters.value.agency.value : null,
        agent: filters.value.agent !== null ? filters.value.agent.value : null,
        category: filters.value.category !== null ? filters.value.category.value : null,
      }
      Inertia.post('/user/secondary/index', fields, { preserveScroll: true })
    }

    const deleteFormDialog = ref(false)
    const addToDeleteId = ref(null)

    const openDeleteForm = (addId) => {
      addToDeleteId.value = addId
      deleteFormDialog.value = true
    }

    const closeDeleteDialog = () => {
      addToDeleteId.value = null
      deleteFormDialog.value = false
    }

    const deleteAdd = () => {
      const fields = {
        operation: 'deleteAdd',
        id: addToDeleteId.value,
        agency: filters.value.agency !== null ? filters.value.agency.value : null,
        agent: filters.value.agent !== null ? filters.value.agent.value : null,
        category: filters.value.category !== null ? filters.value.category.value : null,
      }
      closeDeleteDialog()
      Inertia.post('/user/secondary/index', fields)
    }

    const createAdd = () => {
      Inertia.get('/user/secondary/create')
    }

    return { 
      user,
      breadcrumbs,
      pagination,
      columns,
      asDate,
      statusOptions,
      dateOptions,
      statusLabelForm,
      cantSaveAddStatus,
      createAdd,
      /*appsGridView,*/
      rows,
      showAddFeeTooltip,
      initTooltip,
      onlyNumbersWithDot,
      feeForm,
      canSaveFee,
      saveFee,
      unsetFee,
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

.options-menu {
  width: 322px;
  min-width: 322px !important;
}
</style>