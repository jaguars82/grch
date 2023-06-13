<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Уведомления">
        <template v-slot:content>

          <div class="q-pt-md">
            <q-table
              class="no-shadow datatable"
              bordered
              :rows="rows"
              :columns="columns"
              v-model:pagination="pagination"
              @request="onRequest"
              row-key="id"
              hide-header
            >
              <template v-slot:body="props">
                <q-tr :props="props">
                <q-td key="seen_by_recipient" :props="props">
                    <q-icon size="xs" v-if="props.row.seen_by_recipient" name="drafts" />
                    <q-icon size="xs" v-else name="mail" />
                  </q-td>
                  <q-td key="topic" :props="props">
                    {{ props.row.topic }}
                  </q-td>
                  <q-td key="created_at" :props="props">
                    {{ props.row.created_at }}
                  </q-td>
                  <q-td key="link" :props="props">
                    <inertia-link :href="props.row.link">
                      Подробнее
                    </inertia-link>
                  </q-td>
                </q-tr>
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
import { asDateTime } from '@/helpers/formatter'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
  },
  props: {
    notifications: Array,
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
        label: 'Уведомления',
        icon: 'speaker_notes',
        url: '/user/notification/index',
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
      { name: 'topic', align: 'left', label: 'Тема', field: 'topic', sortable: true },
      { name: 'created_at', required: true, align: 'center', label: 'Дата', field: 'created_at', sortable: true },
      { name: 'seen_by_recipient', align: 'center', label: '', field: 'seen_by_recipient', sortable: true },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.notifications.forEach(row => {
        const processedItem = {
          id: row.id,
          topic: row.topic,
          created_at: asDateTime(row.created_at),
          seen_by_recipient: row.seen_by_recipient,
          link: `/user/notification/view?id=${row.id}`
        }
        processedRows.push(processedItem)
      })
      return processedRows
    })

    const onRequest = (e) => {
      loading.value = true
      Inertia.get(`/user/notification/index`, { page: e.pagination.page, psize: e.pagination.rowsPerPage }, { preserveScroll: true })
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    return { breadcrumbs, columns, rows, loading, pagination, onRequest }
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
