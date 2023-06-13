<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Заявки">
        <template v-slot:content>
          
          <GridTableToggle :defaultMode="appsGridView" />

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
                    {{ props.row.application_number }}
                  </q-td>
                  <q-td key="application_number" :props="props">
                    {{ props.row.author }}
                  </q-td>
                  <q-td key="status" :props="props">
                    {{ props.row.status }}
                  </q-td>
                  <q-td key="client_fio" :props="props">
                    {{ props.row.client_fio }}
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
        label: 'Заявки',
        icon: 'real_estate_agent',
        url: '/user/application/index',
        data: false,
        options: false
      },
    ])

    const pagination = ref({
      page: props.page + 1,
      rowsPerPage: props.psize,
      rowsNumber: props.totalRows
    })

    const columns = [
      { name: 'application_number', required: true, align: 'left', label: 'Номер заявки', field: 'application_number', sortable: true },
      { name: 'author', required: true, align: 'left', label: 'Автор', field: 'author', sortable: true },
      { name: 'status', required: true, align: 'left', label: 'Статус', field: 'status', sortable: true },
      { name: 'client_fio', align: 'center', label: 'ФИО клиента', field: 'client_fio', sortable: true },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.applications.forEach(row => {
        const processedItem = {
          id: row.id,
          author: `${row.author.last_name} ${row.author.first_name}, ${row.author.roleLabel} ${row.author.agency_name}`,
          application_number: row.application_number,
          status: props.statusMap[row.status],
          client_fio: `${row.client_lastname} ${row.client_firstname} ${row.client_middlename}`,
          link: `/user/application/view?id=${row.id}`
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    const appsGridView = ref(false)

    const emitter = useEmitter()
    emitter.on('toggle-grid-table', (e) => appsGridView.value = e)

    const onRequest = (e) => {
      Inertia.get(`/user/application/index`, { page: e.pagination.page, psize: e.pagination.rowsPerPage }, { preserveScroll: true })
    }

    return { breadcrumbs, appsGridView, columns, rows, pagination, onRequest }
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