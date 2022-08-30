<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Заявки">
        <template v-slot:content>
          
          <GridTableToggle :defaultMode="gridView" />

          <div class="q-pa-md">
            <q-table
              :grid="gridView"
              title="Заявки"
              :rows="rows"
              :columns="columns"
              row-key="application_number"
            >
              <template v-slot:body="props">
                <q-tr :props="props">
                  <q-td key="application_number" :props="props">
                    {{ props.row.application_number }}
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
                    <q-card-section class="text-center">
                      Заявка
                      <br>
                      <strong>{{ props.row.application_number }}</strong>
                    </q-card-section>
                    <q-separator />
                    <q-card-section class="flex flex-center" :style="{ fontSize: props.row.calories + 'px' }">
                      <div>{{ props.row.id }} g</div>
                    </q-card-section>
                  </q-card>
                </div>
              </template>

            </q-table>
          </div>
          <div>
            <p v-for="application of applications" :key="application.id">{{ application.application_number }}</p>
          </div>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import ProfileLayout from '../../../Layouts/ProfileLayout.vue'
import Breadcrumbs from '../../../Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '../../../Components/Layout/RegularContentContainer.vue'
import GridTableToggle from '../../../Components/Elements/GridTableToggle.vue'
import useEmitter from '../../../composables/use-emitter'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    GridTableToggle
  },
  props: {
    applications: Array
  },
  setup(props, context) {
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

    let gridView = ref(false)

    const columns = [
      { name: 'application_number', required: true, align: 'left', label: 'Номер заявки', field: 'application_number', sortable: true },
      { name: 'client_fio', align: 'center', label: 'ФИО клиента', field: 'client_fio', sortable: true },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.applications.forEach(row => {
        const processedItem = {
          id: row.id,
          application_number: row.application_number,
          client_fio: `${row.client_lastname} ${row.client_firstname} ${row.client_middlename}`,
          link: `/user/application/view?id=${row.id}`
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    //on('toggle-grid-table', () => console.log('work'))
    const emitter = useEmitter()
    emitter.on('toggle-grid-table', () => console.log('work'))

    return { breadcrumbs, gridView, columns, rows }
  },
  /*mounted: function () {
    this.$on('toggle-grid-table', () => console.log('work'))
  }*/
})
</script>
