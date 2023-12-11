<template>
  <MainLayout>
    <template v-slot:main>
      <q-parallax
        src="/img/search-index-back.jpg"
      >
        <!-- Search form -->
        <div class="row justify-center" :class="{ 'width-98': $q.screen.lt.md, 'width-80': $q.screen.gt.sm, 'items-center': $q.screen.gt.sm }">
          <div class="col-10">
            <div class="row q-col-gutter-none">
              <div class="col-12 col-sm-4 col-md-2 no-padding">
                <q-select square outlined v-model="districtSelect" :options="districtOptions" label="Район" class="search-input" multiple use-chips emit-value map-options options-dense>
                </q-select>
              </div>
              <div class="col-12 col-sm-4 col-md-2 no-padding">
                <q-input square outlined readonly v-model="roomsSelect" label="Количество комнат" class="search-input">
                  <template v-slot:append>
                    <q-icon name="edit_note" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
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
              <div class="col-12 col-sm-4 col-md-3 no-padding">
                <q-select square outlined v-model="developerSelect" :options="developerOptions" label="Застройщик" class="search-input" @update:model-value="onDeveloperSelect" multiple use-chips emit-value map-options options-dense />
              </div>
              <div class="col-12 col-sm-6 col-md-3 no-padding">
                <q-select square outlined v-model="newbuildingComplexesSelect" :options="newbuildingComplexesOptions" label="Жилой комплекс" class="search-input" multiple use-chips emit-value map-options options-dense />
              </div>
              <div class="col-12 col-sm-6 col-md-2 no-padding">
                <q-input square outlined readonly v-model="model" label="Цена" class="search-input">
                  <template v-slot:append>
                    <q-icon name="tune" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
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
          <div class="col-2">
            <q-btn unelevated round icon="search" @click="search" />
            <q-btn unelevated round icon="pin_drop" @click="mapSearch" />
          </div>
        </div>

        <!-- Info cards -->
        <div class="row" :class="{ 'width-98': $q.screen.lt.md, 'width-80': $q.screen.gt.sm }">
          <!-- News slider -->
          <div class="col-12 col-md-6">
            <q-card class="header-card">
              <q-card-section>
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
                      <p class="text-h3 ellipsis">{{ news.title }}</p>
                      <div class="q-mt-md text-center ellipsis-3-lines">
                        {{ news.detail }}
                      </div>
                    </div>
                  </q-carousel-slide>
                </q-carousel>
              </q-card-section>
            </q-card>
          </div>
          <!-- Flats by room -->
          <!-- Flats by deadline -->
        </div>

      </q-parallax>
      <pre>{{ developers }}</pre>
      <pre>{{ districtSelect }}</pre>
    </template>
  </MainLayout>
</template>


<script>
import { ref, computed, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import RoomsAmountButtons from '@/Components/Elements/RoomsAmountButtons.vue'
import useEmitter from '@/composables/use-emitter'
import FlatTypeToggler from '@/Components/Elements/FlatTypeToggler.vue'
import PriceRangeWithToggler from '@/Components/Elements/Ranges/PriceRangeWithToggler.vue'

export default {
  props: {
    searchModel: Object,
    newsList: {
      type: Array
    },
    initialFilterParams: Object,
    // newsDataProvider: Object,
    // actionsDataProvider: Object,
    developerDataProvider: Object,
    agencyDataProvider: Object,
    bankDataProvider: Object,
    districts: Object,
    developers: Object,
    newbuildingComplexes: Object
  },
  components: { MainLayout, RoomsAmountButtons, FlatTypeToggler, PriceRangeWithToggler },
  setup(props) {
    const emitter = useEmitter()

    const districtSelect = ref(null)
    const districtOptions = computed(() => {
      const options = []
      Object.keys(props.districts).forEach(districtId => {
        options.push({ label: props.districts[districtId], value: districtId })
      })
      return options
    })

    const developerSelect = ref(null)
    const developerOptions =  computed(() => {
      const options = []
      Object.keys(props.developers).forEach(developerId => {
        options.push({ label: props.developers[developerId], value: developerId })
      })
      return options
    })

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

    const roomsSelect = ref([])
    emitter.on('rooms-amont-changed', (payload) => {
      roomsSelect.value = payload
    })

    const flatTypeSelect = ref('0')
    emitter.on('flat-type-changed', (payload) => {
      flatTypeSelect.value = payload
    })

    const priceRangeSelect = ref({ min: null, max: null })
    emitter.on('price-changed', (payload) => {
      priceRangeSelect.value = payload
    })

    const priceTypeSelect = ref('0')
    emitter.on('price-type-changed', (payload) => {
      priceTypeSelect.value = payload
    })

    const collectSearchFilter = () => {
      return {
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

    return {
      newsSlide,
      districtSelect,
      districtOptions,
      developerSelect,
      developerOptions,
      onDeveloperSelect,
      roomsSelect,
      flatTypeSelect,
      priceRangeSelect,
      priceTypeSelect,
      newbuildingComplexesSelect,
      newbuildingComplexesOptions,
      search,
      mapSearch,
      model: ref(null),
      options: [
        'Google', 'Facebook', 'Twitter', 'Apple', 'Oracle'
      ]
    }
  },
}
</script>

<style scoped>
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