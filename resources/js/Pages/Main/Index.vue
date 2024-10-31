<template>
  <MainLayout :gutters="false">
    <template v-slot:main>
      <div
        class="row items-center justify-center q-pa-lg header-img"
        style="background-image: url(/img/search-index-back.jpg);"
      >
      <!--<q-parallax
        src="/img/search-index-back.jpg"
      >-->
        <!-- Search form -->
        <div class="row justify-center q-my-lg" :class="{ 'width-98': $q.screen.lt.md, 'width-80': $q.screen.gt.sm, 'items-center': $q.screen.gt.sm }">
          <div class="col-12">
            <!-- Region Selector -->
            <RegionSelector :regions="regions" />
          </div>
          <div :class="{ 'col-12': $q.screen.xs, 'col-10': $q.screen.gt.xs }">
            <div class="row q-col-gutter-none">
              <!-- Sattlement (city, town etc.) -->
              <div class="col-12 col-sm-4 col-md-2 no-padding">
                <q-select
                  square
                  outlined
                  v-model="citySelect"
                  :options="cityOptions"
                  label="Населенный пункт"
                  class="search-input"
                  :class="{ 'rounded-left': true, 'rounded-right': $q.screen.xs }"
                  @update:model-value="onCitySelect"
                  emit-value
                  map-options
                  options-dense
                  dense
                >
                  <template v-slot:option="scope">
                    <q-item v-bind="scope.itemProps">
                      <q-item-section>
                        <span :class="{'text-bold': scope.opt.regionCenter == 1}">
                          {{ scope.opt.label }}
                        </span>
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>
              <!-- District of the sattlement -->
              <div class="col-12 col-sm-4 col-md-2 no-padding">
                <q-select
                  square
                  outlined
                  v-model="districtSelect"
                  :options="districtOptions"
                  label="Район"
                  class="search-input"
                  :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs }"
                  multiple
                  use-chips
                  emit-value
                  map-options
                  options-dense
                  dense
                >
                </q-select>
              </div>
              <!-- Amount of rooms and flat type (standart/euro/studio) -->
              <div class="col-12 col-sm-4 col-md-2 no-padding" style="cursor: pointer !important;">
                <q-input
                  square
                  outlined
                  readonly
                  v-model="roomsSelect"
                  @click="showRoomsPopup = true"
                  label="Комнат"
                  class="search-input"
                  :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs || $q.screen.sm }"
                  dense
                >
                  <template v-slot:append>
                    <q-icon name="edit_note" class="cursor-pointer">
                      <q-popup-proxy
                        v-model="showRoomsPopup"
                        cover
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-card>
                          <q-card-section>
                            <RoomsAmountButtons :roomsAmount="roomsSelect" />
                          </q-card-section>
                          <q-card-section>
                            <FlatTypeToggler :flatType="flatTypeSelect" />
                          </q-card-section>
                        </q-card>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <!-- Developer -->
              <div class="col-12 col-sm-4 col-md-2 no-padding">
                <q-select
                  square
                  outlined
                  v-model="developerSelect"
                  :options="developerOptions"
                  label="Застройщик"
                  class="search-input"
                  :class="{ 'rounded-left': $q.screen.xs || $q.screen.sm, 'rounded-right': $q.screen.xs }"
                  @update:model-value="onDeveloperSelect"
                  multiple
                  use-chips
                  emit-value
                  map-options
                  options-dense
                  dense
                />
              </div>
              <!-- Newbuilding Complex -->
              <div class="col-12 col-sm-4 col-md-2 no-padding">
                <q-select
                  square
                  outlined
                  v-model="newbuildingComplexesSelect"
                  :options="newbuildingComplexesOptions"
                  label="Жилой комплекс"
                  class="search-input"
                  :class="{ 'rounded-left': $q.screen.xs, 'rounded-right': $q.screen.xs }"
                  multiple
                  use-chips
                  emit-value
                  map-options
                  options-dense
                  dense
                />
              </div>
              <!-- Price -->
              <div class="col-12 col-sm-4 col-md-2 no-padding cursor-pointer">
                <q-input
                  square
                  outlined
                  readonly
                  v-model="priceLabel"
                  @click="showPricePopup = true"
                  label="Цена"
                  class="search-input"
                  :class = "{ 'rounded-left': $q.screen.xs, 'rounded-right': true }"
                  dense
                >
                  <template v-slot:append>
                    <q-icon name="tune" class="cursor-pointer">
                      <q-popup-proxy
                        v-model="showPricePopup"
                        cover
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-card class="popup-controls-container">
                          <q-card-section>
                            <PriceRangeWithToggler
                              :priceRange="priceRangeSelect"
                              :priceType="priceTypeSelect"
                              :rangeEdges="{
                                all: { min: initialFilterParams.minFlatPrice, max: initialFilterParams.maxFlatPrice },
                                m2: { min: initialFilterParams.minM2Price, max: initialFilterParams.maxM2Price }
                              }"
                            />
                          </q-card-section>
                        </q-card>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
            </div>
          </div>
          <!-- Form action buttons -->
          <div class="col-2 self-start" :class="{ 'col-2': $q.screen.gt.xs, 'col-12': $q.screen.xs, 'text-right': $q.screen.xs, 'q-mt-sm': $q.screen.xs }">
            <q-btn color="primary" :size="$q.screen.sm ? 'sm' : 'md'" class="text-white q-ml-sm q-mr-xs" unelevated round icon="search" @click="search" />
            <q-btn color="white" :size="$q.screen.sm ? 'sm' : 'md'" class="text-grey-7" unelevated round icon="pin_drop" @click="mapSearch" />
          </div>
        </div>

        <!-- Info cards -->
        <div class="row" :class="{ 'width-98': $q.screen.lt.md, 'width-80': $q.screen.gt.sm }">
          <!-- News slider -->
          <div class="col-12 col-md-6">
            <q-card class="header-card" :class="{ 'q-mt-md': $q.screen.gt.sm, 'q-mt-none': $q.screen.lt.md }">
              <q-card-section :class="{ 'q-pa-none': $q.screen.lt.md }">
                <q-carousel
                  v-if="newsList.length"
                  v-model="newsSlide"
                  infinite
                  autoplay
                  transition-prev="jump-right"
                  transition-next="jump-left"
                  swipeable
                  animated
                  control-color="white"
                  padding
                  arrows
                  height="240px"
                  class="bg-transparent text-white no-scroll overflow-hidden"
                >
                  <q-carousel-slide
                    v-for="news of newsList"
                    :name="news.id"
                    class="column no-wrap flex-center"
                  >
                    <div class="slide-content">
                      <p class="ellipsis" :class="{ 'text-h3': $q.screen.gt.sm, 'text-h4': $q.screen.lt.md }">{{ news.title }}</p>
                      <div class="q-mt-md row" style="height: 200px;">
                        <q-img class="col-4" v-if="news.image" :src="`/uploads/${news.image}`" />
                        <div class="q-px-sm" :class="{'col-8': news.image, 'col-12': !news.image}">
                        <q-badge v-if="news.category === 1" color="orange" class="text-white">Акция</q-badge>
                        <q-badge v-else-if="news.category === 2" color="primary" class="text-white">Новость</q-badge>
                        <div class="text-h5 text-grey">{{ asDateTime(news.created_at) }}</div>
                        <div class="news-content-preview q-mt-sm ellipsis-3-lines">{{ stripHtml(news.detail) }}</div>
                        <div class="full-width q-mt-md text-right">
                          <q-btn outline rounded color="white" size="sm" icon="east" label="Открыть" @click="goToNews(news.id)" />
                        </div>
                        </div>
                      </div>
                    </div>
                  </q-carousel-slide>
                </q-carousel>
              </q-card-section>
            </q-card>
          </div>
          <!-- Flats by room -->
          <div class="col-6 col-md-3">
            <q-card class="header-card q-mt-md" :class="{ 'q-py-md': $q.screen.gt.sm, 'q-py-none': $q.screen.lt.md }">
              <q-card-section>
                <p class="ellipsis text-center text-white" :class="{ 'text-h3': $q.screen.gt.sm, 'text-h4': $q.screen.lt.md }">По количеству комнат</p>
                <div v-for="param of flatsByParams.byRoom" class="q-mt-lg row justify-between">
                  <div class="text-white">{{ param.param }}</div>
                  <div class="text-white">{{ param.val }}</div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- Flats by deadline -->
          <div class="col-6 col-md-3">
            <q-card class="header-card q-mt-md" :class="{ 'q-py-md': $q.screen.gt.sm, 'q-py-none': $q.screen.lt.md }">
              <q-card-section>
                <p class="ellipsis text-center text-white" :class="{ 'text-h3': $q.screen.gt.sm, 'text-h4': $q.screen.lt.md }">По сроку сдачи</p>
                <div v-for="param of flatsByParams.byDeadline" class="q-mt-lg row justify-between">
                  <div class="text-white">{{ param.param }}</div>
                  <div class="text-white">{{ param.val }}</div>
                </div>
              </q-card-section>
            </q-card>
          </div>
        </div>

      <!--</q-parallax>-->
      </div>

      <div class="row">
        <div class="col-12 text-center">
          <h3 class="q-my-md">Застройщики</h3>
        </div>
        <q-virtual-scroll
          class="q-mx-md hide-scrollbar"
          :items="developerDataProvider"
          virtual-scroll-horizontal
          v-slot="{ item, index }"
        >
          <div class="q-mx-sm q-mb-lg"
            :key="index"
          >
            <DeveloperResumeCard :developer="item" />
          </div>
        </q-virtual-scroll>
      </div>
    </template>
  </MainLayout>
</template>


<script>
import { ref, computed, onMounted, watch, watchEffect } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { asDateTime } from '@/helpers/formatter'
import { stripHtml } from '@/helpers/utils'
import { idNameObjToOptions } from '@/composables/formatted-and-processed-data'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import RegionSelector from '@/Components/Elements/RegionSelector.vue'
import RoomsAmountButtons from '@/Components/Elements/RoomsAmountButtons.vue'
import useEmitter from '@/composables/use-emitter'
import FlatTypeToggler from '@/Components/Elements/FlatTypeToggler.vue'
import PriceRangeWithToggler from '@/Components/Elements/Ranges/PriceRangeWithToggler.vue'
import DeveloperResumeCard from '@/Components/Developer/DeveloperResumeCard.vue'

export default {
  props: {
    searchModel: Object,
    newsList: {
      type: Array
    },
    flatsByParams: Object,
    initialFilterParams: Object,
    // newsDataProvider: Object,
    // actionsDataProvider: Object,
    developerDataProvider: Array,
    agencyDataProvider: Array,
    bankDataProvider: Array,
    regions: Object,
    // districts: Object,
    developers: Object,
    newbuildingComplexes: Object
  },
  components: { MainLayout, RegionSelector, RoomsAmountButtons, FlatTypeToggler, PriceRangeWithToggler, DeveloperResumeCard },
  setup(props) {
    const emitter = useEmitter()

    const regionSelect = ref(null)

    const citiesForRegion = ref(null)
    const citySelect = ref(null)
    const cityOptions = computed(() => {
      const options = []
      if (citiesForRegion.value) {
        citiesForRegion.value.forEach(city => {
          options.push({ label: city.name, value: city.id, regionCenter: city.is_region_center })
        })
      }
      return options
    })

    const districtsOfCity = ref(null)
    const districtSelect = ref(null)
    const districtOptions = computed(() => {
      const options = []
      /*Object.keys(props.districts).forEach(districtId => {
        options.push({ label: props.districts[districtId], value: districtId })
      })*/
      if (districtsOfCity.value) {
        districtsOfCity.value.forEach(district => {
          options.push({ label: district.name, value: district.id })
        })
      }
      return options
    })

    const onCitySelect = () => {
      axios.post('/city/get-for-city?id=' + citySelect.value)
      .then(function (response) {
        districtsOfCity.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const showRoomsPopup = ref(false)

    const developerSelect = ref(null)
    const refrashedDevelopers = ref(null)
    const developerOptions = ref(idNameObjToOptions(props.developers))

    watchEffect(() => {
      developerOptions.value = refrashedDevelopers.value
        ? idNameObjToOptions(refrashedDevelopers.value)
        : idNameObjToOptions(props.developers)
    })

    const isLoadingDevelopers = ref(false)
    
    const fetchDevelopers = async (cityId = null, regionId = null, districtId = null) => {
      
      if (Array.isArray(districtId) && districtId.length === 0) {
        districtId = null;
      }

      let action, argumentName, locationId;

      if (districtId) {
        action = 'get-developers-for-city-district';
        argumentName = 'district_id';
        locationId = districtId;
      } else if (cityId) {
        action = 'get-developers-for-city';
        argumentName = 'city_id';
        locationId = cityId;
      } else {
        action = 'get-developers-for-region';
        argumentName = 'region_id';
        locationId = regionId;
      }

      isLoadingDevelopers.value = true; // set the flag of loading developers

      try {
        const response = await axios.post(`/developer/${action}`, { [argumentName]: locationId });
        
        developerOptions.value = idNameObjToOptions(response.data)
        
        await nextTick()

      } catch (error) {
        console.error("Error loading developers:", error);
      } finally {
        isLoadingDevelopers.value = false // reset the loading flag
      }
    }

    // Developers on citySelect/regionSelect/districtSelect change
    watch([citySelect, regionSelect, districtSelect], ([newCityId, newRegionId, newDistrictId]) => {

      developerSelect.value = null
      newbuildingComplexesSelect.value = null

      fetchDevelopers(newCityId, newRegionId, newDistrictId)
    })

    /*const developerOptions =  computed(() => {
      const options = []
      Object.keys(props.developers).forEach(developerId => {
        options.push({ label: props.developers[developerId], value: developerId })
      })
      return options
    })*/

    const onDeveloperSelect = () => {
      axios.post('/newbuilding-complex/get-for-developer?id=' + developerSelect.value)
      .then(function (response) {
        newbuildingComplexesForDevelopers.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const newbuildingComplexesForDevelopers = ref(null)
    const newbuildingComplexesSelect = ref(null)
    const newbuildingComplexesOptions = computed(() => {
      const options = []
      if (newbuildingComplexesForDevelopers.value) {
        newbuildingComplexesForDevelopers.value.forEach(nbc => {
          options.push({ label: nbc.name, value: nbc.id })
        })
      }
      return options
    })

    emitter.on('region-changed', (payload) => {
      regionSelect.value = payload
      axios.post('/city/get-for-region?id=' + payload)
      .then(function (response) {
        citiesForRegion.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    })

    const roomsSelect = ref([])
    emitter.on('rooms-amont-changed', (payload) => {
      roomsSelect.value = payload
    })

    const flatTypeSelect = ref('0')
    emitter.on('flat-type-changed', (payload) => {
      flatTypeSelect.value = payload
    })

    const showPricePopup = ref(false)
    const priceLabel = ref('')
    const priceRangeSelect = ref({ min: null, max: null })
    emitter.on('price-changed', (payload) => {
      priceLabel.value = `${payload.min} - ${payload.max}`
      priceRangeSelect.value = payload
    })

    const priceTypeSelect = ref('0')
    emitter.on('price-type-changed', (payload) => {
      priceTypeSelect.value = payload
    })

    const collectSearchFilter = () => {
      return {
        region_id: regionSelect.value,
        city_id: citySelect.value,
        district: districtSelect.value,
        roomsCount: roomsSelect.value,
        flatType: flatTypeSelect.value ? flatTypeSelect.value : '0',
        developer: developerSelect.value,
        newbuilding_complex: newbuildingComplexesSelect.value,
        priceType: priceRangeSelect.value ? priceTypeSelect.value : '0',
        priceFrom: priceRangeSelect.value.min,
        priceTo: priceRangeSelect.value.max
      }
    }

    const search = () => {
      Inertia.get(`site/search`, { AdvancedFlatSearch: collectSearchFilter() })
    }

    const mapSearch = () => {
      Inertia.get(`site/map`, { MapFlatSearch: collectSearchFilter() })
    }

    const newsSlide = ref(props.newsList[0].id ?? false)
    const goToNews = (newsId) => {
      Inertia.get('/news/view', { id: newsId })
    }

    return {
      asDateTime,
      stripHtml,
      newsSlide,
      goToNews,
      citySelect,
      cityOptions,
      districtSelect,
      districtOptions,
      onCitySelect,
      showRoomsPopup,
      developerSelect,
      developerOptions,
      onDeveloperSelect,
      roomsSelect,
      flatTypeSelect,
      showPricePopup,
      priceLabel,
      priceRangeSelect,
      priceTypeSelect,
      newbuildingComplexesSelect,
      newbuildingComplexesOptions,
      search,
      mapSearch,
    }
  },
}
</script>

<style>
.search-input .q-field__native {
  cursor: pointer !important;
}
.search-input.q-field--outlined .q-field__control:before {
  border-style: none;
}
</style>

<style scoped>
.row.justify-between::before, .row.justify-between::after {
  display: none;
}
.header-img {
  background-repeat: no-repeat;
  background-size: cover;
}
.width-80 {
  width: 80%;
}
.width-98 {
  width: 98%;
}
.header-card {
  background-color: rgba(0,0,0,.6);
}
.slide-content {
  max-width: 100%;
  max-height: 100%;
  overflow: hidden;
}
.search-input {
  background-color: rgba(255,255,255,.7);
  /*cursor: pointer !important;*/
}
.rounded-left {
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
}
.rounded-right {
  border-top-right-radius: 20px;
  border-bottom-right-radius: 20px;
}
.popup-controls-container {
  width: 300px;
}
.no-padding {
  padding-right: 0;
  padding-left: 0;
  padding-top: 0;
  padding-bottom: 0;
}
</style>