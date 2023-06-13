<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Коммерческие предложения">
        <template v-slot:content>

          <div class="q-mb-md text-right">
            <q-btn unelevated icon="event" label="Выбрать дату" color="primary">
              <q-popup-proxy
                cover
                transition-show="scale"
                transition-hide="scale"
              >
                <q-date
                  v-model="date"
                  :events="events"
                  @update:model-value="onDateSelect"
                >
                  <div class="row items-center justify-end q-gutter-sm">
                    <q-btn label="Закрыть" color="primary" flat v-close-popup />
                    <q-btn label="Сбросить" color="primary" flat @click="onDateReset" />
                  </div>
                </q-date>
              </q-popup-proxy>
            </q-btn>
          </div>

          <loading v-if="loading" size="md" text="Загрузка данных" />
          <q-table
            class="no-shadow datatable"
            v-else
            :rows="rows"
            :columns="columns"
            v-model:pagination="pagination"
            @request="onRequest"
            row-key="id"
            bordered
          >
            <template v-slot:body="props">
              <q-tr :props="props">
              <q-td key="number" :props="props">
                  {{ props.row.number }}
                </q-td>
                <q-td key="created_at" :props="props">
                  {{ props.row.created_at }}
                </q-td>
                <q-td key="flats" :props="props" class="text-left">
                  <div v-for="flat in props.row.flats" :key="flat.id">
                    {{ flat.newbuildingComplex.name }} > {{ flat.newbuilding.name }} > №{{ flat.number }}
                  </div>
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

          </q-table>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import { asDateTime } from '@/helpers/formatter'
import Loading from '@/Components/Elements/Loading.vue'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    Loading
  },
  props: {
    commercials: Array,
    events: Array,
    totalRows: String,
    page: Number,
    psize: Number,
  },
  setup(props) {
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
        label: 'Коммерческие предложения',
        icon: 'share',
        url: '/user/commercial/index',
        data: false,
        options: false
      },
    ])

    const pagination = ref({
      page: props.page + 1,
      rowsPerPage: props.psize,
      rowsNumber: props.totalRows
    })

    const loading = ref(false)

    const date = ref(null)

    const columns = [
      { name: 'number', align: 'left', label: 'Номер', field: 'number', sortable: true },
      { name: 'created_at', required: true, align: 'center', label: 'От', field: 'created_at', sortable: true },
      { name: 'flats', required: true, align: 'center', label: 'Объекты', field: 'flats', sortable: false },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
      { name: 'archive', align: 'center', label: '', field: 'archive', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.commercials.forEach(row => {
        const processedItem = {
          id: row.id,
          number: row.number,
          created_at: asDateTime(row.created_at),
          flats: row.flats,
          link: `/user/commercial/view?id=${row.id}`
        }
        processedRows.push(processedItem)
      })
      return processedRows
    })

    const onRequest = (e) => {
      loading.value = true
      Inertia.get(`/user/commercial/index`, { page: e.pagination.page, psize: e.pagination.rowsPerPage }, { preserveScroll: true })
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    const onDateSelect = function() {
      loading.value = true
      Inertia.get(`/user/commercial/index`, { operation: 'selectByDate', ondate: date.value, page: pagination.value.page, psize: pagination.value.rowsPerPage }, { preserveState: true, preserveScroll: true })
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    function setToday() {
      const currentDay = new Date()
      const yyyy = currentDay.getFullYear()
      let mm = currentDay.getMonth() + 1
      let dd = currentDay.getDate()

      if (dd < 10) dd = '0' + dd
      if (mm < 10) mm = '0' + mm

      const formattedToday = yyyy + '/' + mm + '/' + dd
      date.value = formattedToday
    }

    const onDateReset = function() {
      setToday()
      loading.value = true
      Inertia.post(`/user/commercial/index`, { operation: 'resetDate' })
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    const moveToArchive = function(id) {
      Inertia.post(`/user/commercial/index`, { operation: 'moveToArchive', id: id })
      Inertia.on('finish', (event) => {
        //loading.value = false
      })
    }

    onMounted(() => {
      setToday()
    })

    return { breadcrumbs, loading, date, columns, rows, pagination, onRequest, onDateSelect, onDateReset, moveToArchive }
  },
}
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