<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer>
        <template v-slot:content>

          <div v-if="user.role !== 'admin'">
            <inertia-link href="/user/support-ticket/create">
              <q-btn color="primary" unelevated label="Создать запрос" />
            </inertia-link>
          </div>

          <GridTableToggle :defaultMode="ticketsGridView" />
          <q-table
            class="q-mt-md no-shadow datatable"
            bordered
            :grid="ticketsGridView"
            :columns="columns"
            :rows="rows"
            v-model:pagination="pagination"
            @request="onRequest"
            row-key="ticket_number"
          >
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="ticket_number" :props="props">
                  {{ props.row.ticket_number }}
                </q-td>
                <q-td key="title" :props="props">
                  <q-icon color="orange" class="q-pr-xs" v-if="props.row.has_unread_mess" name="mark_chat_unread"></q-icon>
                  <span :class="{'text-strong': props.row.has_unread_mess}">{{ props.row.title }}</span>
                </q-td>
                <q-td key="created_at" :props="props">
                  {{ props.row.created_at }}
                </q-td>
                <q-td key="link" :props="props">
                  <inertia-link :href="props.row.link">
                    Открыть
                  </inertia-link>
                </q-td>
              </q-tr>
            </template>
          
            <template v-slot:item="props">
              <div class="q-pa-xs col-xs-12 col-sm-6 col-md-4">
                <q-card>
                  <q-card-section>
                    <inertia-link :href="`/user/support-ticket/view?id=${props.row.id}`">
                      <div>
                        <p class="q-mb-xs text-h4 text-center">{{ props.row.ticket_number }}</p>
                        <p class="text-subtitle1 text-center">от {{ props.row.created_at }}</p>
                        <p class="text-h5 text-center">{{ props.row.title }}</p>
                      </div>
                    </inertia-link>
                  </q-card-section>
                </q-card>
              </div>
            </template>
          </q-table>
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
import { asDateTime } from '@/helpers/formatter' 
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    GridTableToggle
  },
  props: {
    tickets: Array,
    totalRows: String,
    page: Number,
    psize: Number,
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
        label: 'Техподдержка',
        icon: 'support_agent',
        url: '/user/support/index',
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

    const columns = [
      { name: 'ticket_number', required: true, align: 'left', label: 'Номер', field: 'ticket_number', sortable: true },
      { name: 'title', align: 'left', label: 'Тема', field: 'title', sortable: false },
      { name: 'created_at', align: 'center', label: 'Создан', field: 'created_at', sortable: true },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.tickets.forEach(row => {
        const processedItem = {
          id: row.id,
          ticket_number: row.ticket_number,
          title: row.title,
          created_at: asDateTime(row.created_at),
          link: `/user/support-ticket/view?id=${row.id}`,
          has_unread_mess: (user.value.role === 'admin' && row.has_unread_messages_from_author) || (user.value.role !== 'admin' && row.has_unread_messages_from_support) ? true : false
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    const ticketsGridView = ref(false)
    
    const emitter = useEmitter()
    emitter.on('toggle-grid-table', (e) => ticketsGridView.value = e)

    const onRequest = (e) => {
      loading.value = true
      Inertia.get(`/user/support/index`, { page: e.pagination.page, psize: e.pagination.rowsPerPage }, { preserveScroll: true })
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    return { user, breadcrumbs, columns, rows, asDateTime, ticketsGridView, loading, pagination, onRequest }
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
