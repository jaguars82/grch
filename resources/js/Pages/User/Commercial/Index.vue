<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Коммерческие предложения">
        <template v-slot:content>
          <q-table
            :rows="rows"
            :columns="columns"
            :pagination="{ rowsPerPage: 25 }"
            row-key="id"
            hide-bottom
          >
            <template v-slot:body="props">
              <q-tr :props="props">
              <q-td key="number" :props="props">
                  {{ props.row.number }}
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

          </q-table>
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
import { asDateTime } from '@/helpers/formatter'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer
  },
  props: {
    commercials: Array
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

    const columns = [
      { name: 'number', align: 'left', label: 'Номер', field: 'number', sortable: true },
      { name: 'created_at', required: true, align: 'center', label: 'От', field: 'created_at', sortable: true },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.commercials.forEach(row => {
        const processedItem = {
          id: row.id,
          number: row.number,
          created_at: asDateTime(row.created_at),
          link: `/user/commercial/view?id=${row.id}`
        }
        processedRows.push(processedItem)
      })
      return processedRows
    })

    return { breadcrumbs, columns, rows }
  },
}
</script>
