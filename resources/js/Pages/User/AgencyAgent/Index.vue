<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="`Агенты «${agency.name}»`">
        <template v-slot:content>
          <div class="q-py-md">
            <a :href="`/user/agency-agent/create?agencyId=${agency.id}`">
              <q-btn color="primary" unelevated label="Добавить агента" />
            </a>
            <span class="q-px-md">Кличество агентов: {{ agentAmount }}</span>
          </div>

          <!-- TODO Uncomment below switch when agent's card is ready -->
          <!--<GridTableToggle :defaultMode="agentsGridView" />-->

          <div class="q-pt-md">
            <q-table
              class="q-mt-md no-shadow"
              bordered
              :grid="agentsGridView"
              :rows="rows"
              :columns="columns"
              :pagination="{ rowsPerPage: 500 }"
              row-key="id"
              hide-bottom
            >
              <template v-slot:body="props">
                <q-tr :props="props">
                  <q-td key="id" :props="props">
                    {{ props.row.id }}
                  </q-td>
                  <q-td key="fio" :props="props">
                    {{ props.row.fio }}
                  </q-td>
                  <q-td key="email" :props="props">
                    {{ props.row.email }}
                  </q-td>
                  <q-td key="phone" :props="props">
                    {{ props.row.phone }}
                  </q-td>
                  <q-td key="edit" :props="props">
                    <a :href="props.row.edit">
                      <q-icon name="edit" />
                    </a>
                  </q-td>
                  <q-td key="edit" :props="props">
                    <a :href="props.row.delete">
                      <q-icon name="delete" />
                    </a>
                  </q-td>
                </q-tr>
              </template>

              <!--
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
              -->

            </q-table>
          </div>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
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
    agency: Array,
    agents: Array
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
        label: 'Агенты',
        icon: 'group',
        url: '/user/agency-agent/index',
        data: false,
        options: false
      },
    ])

    const agentAmount = computed(() => {
      return props.agents.length
    })

    const columns = [
      { name: 'id', required: true, align: 'left', label: 'ID', field: 'id', sortable: true },
      { name: 'fio', required: true, align: 'center', label: 'ФИО', field: 'fio', sortable: true },
      { name: 'email', align: 'center', label: 'Email', field: 'email', sortable: true },
      { name: 'phone', align: 'center', label: 'Телефон', field: 'phone', sortable: true },
      { name: 'edit', align: 'center', label: '', field: 'edit', sortable: false },
      { name: 'delete', align: 'center', label: '', field: 'delete', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.agents.forEach(row => {
        const processedItem = {
          id: row.id,
          fio: `${row.last_name} ${row.first_name} ${row.middle_name ? row.middle_name : ''}`,
          email: row.email,
          phone: row.phone,
          edit: `/user/agency-agent/update?id=${row.id}&agencyId=${props.agency.id}`,
          delete: `/user/agency-agent/delete?id=${row.id}&agencyId=${props.agency.id}`
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    const agentsGridView = ref(false)

    const emitter = useEmitter()
    emitter.on('toggle-grid-table', (e) => agentsGridView.value = e)

    return { breadcrumbs, agentAmount, agentsGridView, columns, rows }
  },
})
</script>