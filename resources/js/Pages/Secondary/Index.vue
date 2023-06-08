<template>
  <MainLayout :secondaryColumns="3">
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <h3 class="text-center">Вторичная продажа</h3>
      <div v-for="advertisement of advertisements" :key="advertisement.id">
        <div v-if="advertisement.statusLabels.length" style="margin-bottom: -25px;">
          <q-chip square color="primary" class="text-white" v-for="status of advertisement.statusLabels">{{ status.type.name }}</q-chip>
        </div>
        <SecondaryRoomListItem
          v-for="room of advertisement.secondary_room"
          :key="room.id"
          :room="room"
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
          :max-pages="8"
          @update:model-value="goToPage(currentPage)"
        />
      </div>
    </template>
    <template v-slot:secondary>
      <div class="row justify-center q-my-sm q-px-sm">
        <q-btn color="primary" unelevated label="Добавить объявление" icon="post_add" @click="goToCreateAdd" />
      </div>
      <SecondaryFilter
        :filterParams="filterFields"
        :ranges="ranges"
        :secondaryCategories="secondaryCategories"
        :districts="districts"
        :streetList="streetList"
      />
    </template>
  </MainLayout>
</template>
  
<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import SecondaryRoomListItem from "@/Components/SecondaryRoom/SecondaryRoomListItem.vue"
import SecondaryFilter from '@/Pages/Secondary/partials/SecondaryFilter.vue'
import useEmitter from '@/composables/use-emitter'
  
export default {
  props: {
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
    streetList: {
      type: Array
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading, SecondaryRoomListItem, SecondaryFilter
  },
  setup(props) {

    const currentPage = ref(props.pagination.currPage)

    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: 'native'
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
      filterFields.value.street = fields.street ? fields.street : ''
      //console.log(filterFields.value.deal_type)
      Inertia.get('/secondary', { filter: filterFields.value }, { only: ['advertisements', 'ranges', 'pagination', 'filterParams'], preserveState: true, preserveScroll: true })
    }

    const emitter = useEmitter()
    emitter.on('secondary-filter-changed', (e) => filterSecondaryRooms(e))

    const goToCreateAdd = () => {
      Inertia.get('user/secondary/create')
    }

    return { breadcrumbs, currentPage, filterFields, goToPage, goToCreateAdd }
  }
}
</script>