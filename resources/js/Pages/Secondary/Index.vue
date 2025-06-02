<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: $q.platform.is.mobile ? false : true } }">

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <h3 class="text-center">Вторичная продажа</h3>
      <div class="q-mx-md" v-for="advertisement of advertisements" :key="advertisement.id">
        <SecondaryRoomListItem
          v-for="room of advertisement.secondary_room"
          :key="room.id"
          :room="room"
          :statusLabels="advertisement.statusLabels"
          :created="advertisement.creation_date"
          :author="{
            db: advertisement.author_DB ? advertisement.author_DB : null,
            info: advertisement.author_info
          }"
          :advId="advertisement.id"
        />          
      </div>

      <div class="q-pa-lg flex flex-center">
        <q-pagination
          v-model="currentPage"
          :max="pagination.totalPages"
          :max-pages="$q.screen.xs ? 4 : 8"
          @update:model-value="goToPage(currentPage)"
        />
      </div>
      <Messenger />
    </template>

    <template v-slot:right-drawer>
      <div v-if="user.agency_id" class="row justify-center q-my-sm q-px-sm">
        <q-btn color="primary" unelevated label="Добавить объявление" icon="post_add" @click="goToCreateAdd" />
      </div>
      <SecondaryFilter
        :filterParams="filterFields"
        :ranges="ranges"
        :secondaryCategories="secondaryCategories"
        :agencies="agencies"
        :statusLabelTypes="statusLabelTypes"
        :districts="districts"
        :streetList="streetList"
      />
    </template>

  </MainLayout>
</template>
  
<script>
import { ref, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'
import { userInfo } from '@/composables/shared-data'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import SecondaryRoomListItem from "@/Components/SecondaryRoom/SecondaryRoomListItem.vue"
import SecondaryFilter from '@/Pages/Secondary/partials/SecondaryFilter.vue'
import Messenger from '@/Vidgets/Messenger/Messenger'
import useEmitter from '@/composables/use-emitter'
  
export default {
  props: {
    flash: Object,
    advertisements: {
      type: Array,
      default: []
    },
    pagination: {
      type: Object,
      default: {
        currPage: 1
      }
    },
    filterParams: {
      type: Object,
      default: {}
    },
    ranges: {
      type: Object
    },
    secondaryCategories: {
      type: Object,
    },
    districts: {
      type: Array
    },
    agencies: {
      type: Array
    },
    statusLabelTypes: {
      type: Object
    },
    streetList: {
      type: Array
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading, SecondaryRoomListItem, SecondaryFilter, Messenger
  },
  setup(props) {
    const $q = useQuasar();

    onMounted(() => {
      if (props.flash?.success) {
        $q.notify({
          type: 'positive',
          message: props.flash.success,
          position: 'top',
        })
      }
    })

    const { user } = userInfo()

    const currentPage = ref(props.pagination.currPage)

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
        label: 'Вторичная продажа',
        icon: 'home_work',
        url: '/secondary',
        data: false,
        options: false
      },
    ])

    const filterFields = ref({
      deal_type: props.filterParams.deal_type,
      category: props.filterParams.category,
      priceFrom: props.filterParams.priceFrom,
      priceTo: props.filterParams.priceTo,
      agency: props.filterParams.agency,
      statusLabel: props.filterParams.statusLabel,
      rooms: props.filterParams.rooms,
      areaFrom: props.filterParams.areaFrom,
      areaTo: props.filterParams.areaTo,
      district: props.filterParams.district,
      street: props.filterParams.street,
      floorFrom: props.filterParams.floorFrom,
      floorTo: props.filterParams.floorTo,
      totalFloorsFrom: props.filterParams.totalFloorsFrom,
      totalFloorsTo: props.filterParams.totalFloorsTo,
      kitchenAreaFrom: props.filterParams.kitchenAreaFrom,
      kitchenAreaTo: props.filterParams.kitchenAreaTo,
      livingAreaFrom: props.filterParams.livingAreaFrom,
      livingAreaTo: props.filterParams.livingAreaTo,
      balconyFrom: props.filterParams.balconyFrom,
      balconyTo: props.filterParams.balconyTo,
      loggiaFrom: props.filterParams.loggiaFrom,
      loggiaTo: props.filterParams.loggiaTo,
      windowviewStreet: props.filterParams.windowviewStreet,
      windowviewYard: props.filterParams.windowviewYard,
      panoramicWindows: props.filterParams.panoramicWindows,
      builtYearFrom: props.filterParams.builtYearFrom,
      builtYearTo: props.filterParams.builtYearTo,
      concierge: props.filterParams.concierge,
      rubbishChute: props.filterParams.rubbishChute,
      gasPipe: props.filterParams.gasPipe,
      closedTerritory: props.filterParams.closedTerritory,
      playground: props.filterParams.playground,
      undergroundParking: props.filterParams.undergroundParking,
      groundParking: props.filterParams.groundParking,
      openParking: props.filterParams.openParking,
      multilevelParking: props.filterParams.multilevelParking,
      barrier: props.filterParams.barrier,
    })
  
    const goToPage = function (page) {
      Inertia.get('/secondary', { page: page, filter: filterFields.value })
    }

    const filterSecondaryRooms = (fields) => {
      filterFields.value.deal_type = fields.deal_type ? fields.deal_type.value : ''
      filterFields.value.category = fields.category ? fields.category.value : ''
      filterFields.value.priceFrom = fields.price.min ? fields.price.min : ''
      filterFields.value.priceTo = fields.price.max ? fields.price.max : ''
      filterFields.value.rooms = fields.rooms ? fields.rooms : ''
      filterFields.value.areaFrom = fields.area.min ? fields.area.min : ''
      filterFields.value.areaTo = fields.area.max ? fields.area.max : ''
      filterFields.value.floorFrom = fields.floor.min ? fields.floor.min : ''
      filterFields.value.floorTo = fields.floor.max ? fields.floor.max : ''
      filterFields.value.totalFloorsFrom = fields.totalFloors.min ? fields.totalFloors.min : ''
      filterFields.value.totalFloorsTo = fields.totalFloors.max ? fields.totalFloors.max : ''
      filterFields.value.kitchenAreaFrom = fields.kitchenArea.min ? fields.kitchenArea.min : ''
      filterFields.value.kitchenAreaTo = fields.kitchenArea.max ? fields.kitchenArea.max : ''
      filterFields.value.livingAreaFrom = fields.livingArea.min ? fields.livingArea.min : ''
      filterFields.value.livingAreaTo = fields.livingArea.max ? fields.livingArea.max : ''
      filterFields.value.balconyFrom = fields.balconyAmount.min ? fields.balconyAmount.min : ''
      filterFields.value.balconyTo = fields.balconyAmount.max ? fields.balconyAmount.max : ''
      filterFields.value.loggiaFrom = fields.loggiaAmount.min ? fields.loggiaAmount.min : ''
      filterFields.value.loggiaTo = fields.loggiaAmount.max ? fields.loggiaAmount.max : ''
      filterFields.value.windowviewStreet = fields.windowviewStreet ? fields.windowviewStreet : ''
      filterFields.value.windowviewYard = fields.windowviewYard ? fields.windowviewYard : ''
      filterFields.value.panoramicWindows = fields.panoramicWindows ? fields.panoramicWindows : ''
      filterFields.value.builtYearFrom = fields.builtYear.min ? fields.builtYear.min : ''
      filterFields.value.builtYearTo = fields.builtYear.max ? fields.builtYear.max : ''
      filterFields.value.concierge = fields.concierge ? fields.concierge : ''
      filterFields.value.rubbishChute = fields.rubbishChute ? fields.rubbishChute : ''
      filterFields.value.gasPipe = fields.gasPipe ? fields.gasPipe : ''
      filterFields.value.closedTerritory = fields.closedTerritory ? fields.closedTerritory : ''
      filterFields.value.playground = fields.playground ? fields.playground : ''
      filterFields.value.undergroundParking = fields.undergroundParking ? fields.undergroundParking : ''
      filterFields.value.groundParking = fields.groundParking ? fields.groundParking : ''
      filterFields.value.openParking = fields.openParking ? fields.openParking : ''
      filterFields.value.multilevelParking = fields.multilevelParking ? fields.multilevelParking : ''
      filterFields.value.barrier = fields.barrier ? fields.barrier : ''

      let districtFields = []
      if (fields.district && fields.district.length > 0) {
        fields.district.forEach(elem => districtFields.push(elem.value))
      }
      filterFields.value.district = districtFields.length > 0 ? districtFields : ''

      let agencyFields = []
      if (fields.agency && fields.agency.length > 0) {
        fields.agency.forEach(elem => agencyFields.push(elem.value))
      }
      filterFields.value.agency = agencyFields.length > 0 ? agencyFields : ''

      let statusLabelFields = []
      if (fields.statusLabel && fields.statusLabel.length > 0) {
        fields.statusLabel.forEach(elem => statusLabelFields.push(elem.value))
      }
      filterFields.value.statusLabel = statusLabelFields.length > 0 ? statusLabelFields : ''

      filterFields.value.street = fields.street ? fields.street : ''
      //console.log(filterFields.value.deal_type)
      Inertia.get('/secondary', { filter: filterFields.value }, { only: ['advertisements', 'ranges', 'pagination', 'filterParams'], preserveState: true, preserveScroll: true })
    }

    const emitter = useEmitter()
    emitter.on('secondary-filter-changed', (e) => filterSecondaryRooms(e))

    const goToCreateAdd = () => {
      Inertia.get('/user/secondary/create')
    }

    return { user, breadcrumbs, currentPage, filterFields, goToPage, goToCreateAdd }
  }
}
</script>