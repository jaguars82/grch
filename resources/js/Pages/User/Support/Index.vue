<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <q-table
        grid
        :columns="columns"
        :rows="rows"
        row-key="ticket_number"
      >
        <template v-slot:item="props">
          <div class="q-pa-xs col-xs-12 col-sm-6 col-md-4">
            <q-card>
              <q-card-section>
                <inertia-link :href="`/user/support-ticket/view?id=${props.row.id}`">
                  <div>
                    <span>{{ props.row.ticket_number }}</span>
                  </div>
                </inertia-link>
              </q-card-section>
            </q-card>
          </div>
        </template>
      </q-table>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref } from 'vue'
import ProfileLayout from '../../../Layouts/ProfileLayout.vue'
import Breadcrumbs from '../../../Components/Layout/Breadcrumbs.vue'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs
  },
  props: {
    tickets: Array
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
        label: 'Техподдержка',
        icon: 'support_agent',
        url: '/user/support/index',
        data: false,
        options: false
      },
    ])

    const columns = [
      { name: 'ticket_number', required: true, align: 'left', label: 'Номер', field: 'ticket_number', sortable: true },
      { name: 'title', align: 'left', label: 'Тема', field: 'title', sortable: false },
    ]

    const rows = props.tickets

    console.log(props.tickets)

    return { breadcrumbs, columns, rows }
  },
})
</script>
