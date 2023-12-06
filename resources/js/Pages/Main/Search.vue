<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <FlatListItem
        v-for="flat of dataProvider"
        class="q-ml-md q-mt-md"
        :flat="flat"
      />
      <div class="q-pa-lg flex flex-center">
        <q-pagination
          v-model="currentPage"
          :max="pagination.totalPages"
          :max-pages="8"
          @update:model-value="goToPage(currentPage)"
        />
      </div>
      <pre>{{ searchModel }}</pre>
      <pre>{{ newsDataProvider }}</pre>
      <pre>{{ itemsCount }}</pre>
      <pre>{{ regions }}</pre>
      <pre>{{ cities }}</pre>
      <pre>{{ districts }}</pre>
      <pre>{{ developers }}</pre>
      <pre>{{ newbuildingComplexes }}</pre>
      <pre>{{ positionArray }}</pre>
      <pre>{{ materials }}</pre>
      <pre>{{ deadlineYears }}</pre>
    </template>
  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from '@/Components/Elements/Loading.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'

export default {
  props: {
    searchModel: {
      type: Object,
      default: {}
    },
    dataProvider: {
      type: Array,
      default: []
    },
    pagination: {
      type: Object,
      default: {}
    },
    newsDataProvider: {
      type: Array,
      default: []
    },
    itemsCount: {
      type: Number,
      default: 0
    },
    regions: {
      type: Object,
      default: {}
    },
    cities: {
      type: Object,
      default: {}
    },
    districts: {
      type: Object,
      default: {}
    },
    developers: {
      type: Object,
      default: {}
    },
    newbuildingComplexes: {
      type: Object,
      default: {}
    },
    positionArray: {
      type: Array,
      default: []
    },
    materials: {
      type: Object,
      default: {}
    },
    deadlineYears: {
      type: Object,
      default: {}
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading, FlatListItem
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
        label: 'Расширенный поиск',
        icon: 'search',
        url: '/site/search',
        data: false,
        options: false
      },
    ])

    const currentPage = ref(props.pagination.page + 1)

    const goToPage = function (page) {
      Inertia.get('/site/search', { 'list-page': page, AdvancedFlatSearch: props.searchModel.AdvancedFlatSearch }/*, { preserveState: true }*/)
    }

    return { breadcrumbs, currentPage, goToPage  }
  }
}
</script>