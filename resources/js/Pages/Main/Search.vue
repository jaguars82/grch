<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: true } }">
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <!-- List of Objects -->
      <FlatListItem
        v-for="flat of dataProvider"
        class="q-mx-md q-mt-md"
        :flat="flat"
      />
      <div class="q-pa-lg flex flex-center">
        <q-pagination
          v-model="currentPage"
          :max="pagination.totalPages"
          :max-pages="$q.screen.xs ? 4 : 8"
          @update:model-value="goToPage(currentPage)"
        />
      </div>
    </template>

    <template v-slot:right-drawer>
      <FilterConfirmDialog />

      <div class="q-pa-md">
        <AdvancedFlatFilter
          :searchModel="searchModel.AdvancedFlatSearch"
          :regions="regions"
          :cities="cities"
          :districts="districts"
          :developers="developers"
          :newbuildingComplexes="newbuildingComplexes"
          :positions="positionArray"
          :material="materials"
          :deadlineYears="deadlineYears"
          :rangeEdges="rangeEdges"
          :forCurrentRegion="forCurrentRegion"
        />
      </div>
    </template>

  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
// import useEmitter from '@/composables/use-emitter'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from '@/Components/Elements/Loading.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import AdvancedFlatFilter from '@/Pages/Main/partials/AdvancedFlatFilter.vue'
import FilterConfirmDialog from '@/Pages/Main/partials/FilterConfirmDialog.vue'

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
      type: Object,
      default: {}
    },
    materials: {
      type: Object,
      default: {}
    },
    deadlineYears: {
      type: Object,
      default: {}
    },
    rangeEdges: Object,
    forCurrentRegion: Object,
  },
  components: {
    MainLayout, Breadcrumbs, Loading, FlatListItem, AdvancedFlatFilter, FilterConfirmDialog
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

    // Old variant of search: emmidiatly on filter change
    /*const emitter = useEmitter()
    emitter.on('flat-filter-changed', payload => {      
      axios.post('/site/pre-search', { AdvancedFlatSearch: payload })
      .then(function (response) {
        newSearchPrecount.value = response.data
        console.log(newSearchPrecount.value)
      })
      .catch(function (error) {
        console.log(error)
      })
    })*/

    return { breadcrumbs, currentPage, goToPage }
  }
}
</script>