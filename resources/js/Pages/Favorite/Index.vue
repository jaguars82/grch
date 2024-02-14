<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <FlatListItem
        v-for="favorite of activeDataProvider"
        class="q-mx-md q-mt-md"
        :flat="favorite.flat"
      />
      <div class="q-pa-lg flex flex-center">
        <q-pagination
          v-model="currentActivePage"
          :max="paginationActive.totalPages"
          :max-pages="$q.screen.xs ? 4 : 8"
          @update:model-value="goToActivePage(currentActivePage)"
        />
      </div>
    </template>

  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import useEmitter from '@/composables/use-emitter'
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
    activeDataProvider: {
      type: Array,
      default: []
    },
    archiveDataProvider: {
      type: Array,
      default: []
    },
    paginationActive: {
      type: Object,
      default: {}
    },
    paginationArchive: {
      type: Object,
      default: {}
    },
    activeItemsCount: {
      type: Number,
      default: 0
    },
    archiveItemsCount: {
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
    /*newbuildingComplexes: {
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
    rangeEdges: Object,*/
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
        label: 'Избранное',
        icon: 'bookmark',
        url: '/site/favorite',
        data: false,
        options: false
      },
    ])

    const currentActivePage = ref(props.paginationActive.page + 1)
    const currentArchivePage = ref(props.paginationArchive.page + 1)

    const goToActivePage = function (page) {
      Inertia.get('/favorite', { 'page': page, /*AdvancedFlatSearch: props.searchModel.AdvancedFlatSearch*/ })
    }

    /*const emitter = useEmitter()
    emitter.on('flat-filter-changed', payload => {
      Inertia.get('/site/search', { AdvancedFlatSearch: payload }, { preserveState: true })
    })*/

    return { breadcrumbs, currentActivePage, currentArchivePage /*goToPage */ }
  }
}
</script>