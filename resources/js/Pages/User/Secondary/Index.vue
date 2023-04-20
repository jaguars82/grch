<template>
    <ProfileLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <RegularContentContainer title="Вторичная недвижимость">
          <template v-slot:content>
            <pre>{{ advertisements[0] }}</pre>
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
    user: Array,
    advertisements: Array,
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
        label: 'Вторичная недвижимость',
        icon: 'home_work',
        url: '/user/secondary/index',
        data: false,
        options: false
      },
    ])

    const columns = [
      { name: 'id', required: true, align: 'left', label: 'id', field: 'id', sortable: true },
      { name: 'objects', required: true, align: 'left', label: 'Объект', field: 'object', sortable: false },      
      { name: 'author', required: false, align: 'left', label: 'Автор', field: 'author', sortable: true },
      { name: 'agency', required: true, align: 'left', label: 'Агентство', field: 'agency', sortable: true },
      { name: 'origin_type', align: 'center', label: '', field: 'origin_type', sortable: false },
      { name: 'link', align: 'center', label: '', field: 'link', sortable: false },
      { name: 'edit', align: 'center', label: '', field: 'edit', sortable: false },
    ]

    /*const rows = computed(() => {
      const processedRows = []
      props.advertisements.forEach(row => {
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
    })*/

    /*const appsGridView = ref(false)

    const emitter = useEmitter()
    emitter.on('toggle-grid-table', (e) => appsGridView.value = e)*/

    return { breadcrumbs, columns/*appsGridView, rows*/ }
  },
})
</script>