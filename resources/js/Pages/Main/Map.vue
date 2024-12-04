<template>
  <MainLayout :gutters="false" :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: true } }">
    <template v-slot:main>
      <template v-if="complexes !== undefined">
        <YandexMap
          :settings="yaMapsSettings"
          :coordinates="initCoords"
          :options="{ autoFitToViewport: 'always' }"
          :zoom="12"
          @balloonopen="adjustBaloonHeight"
        >
          <template v-if="complexes.length">
            <YandexClusterer>
              <YandexMarker
                v-for="complex of complexes"
                :key="complex.id"
                :marker-id="complex.id"
                type="Point"
                :coordinates="[complex.longitude, complex.latitude]"
              >
                <template #component>
                  <q-img class="baloon-complex-image" :src="complex.images.length ? `/uploads/${complex.images[0].file}` : '/img/newbuilding-complex.png'" />
                  <div class="row">
                    <div class="col-10">
                      <p class="q-my-xs text-h4">{{ complex.name }}</p>
                    </div>
                    <div class="col-2">
                      <q-img fit="scale-down" class="baloon-complex-logo" :src="`/uploads/${complex.logo}`" />
                    </div>
                  </div>
                  <p v-if="complex.address" class="q-mb-sm text-grey-8 text-ellipsis">{{ complex.address }}</p>
                  <p class="q-mb-sm">Сдача: {{ complex.nearestDeadline }}</p>
                  <div class="q-mb-sm">
                    <ParamPair
                      v-for="priceByFlat of complex.flats_by_room"
                      :paramName="priceByFlat.label"
                      :paramValue="priceByFlat.price"
                      :link="priceByFlat.search_url"
                      :dense="true"
                    />
                  </div>
                  <div class="q-mt-sm text-center">
                    <q-btn size="sm" unelevated color="orange" @click="goToComplex(complex.id)">Перейти к ЖК</q-btn>
                  </div>
                  <!--
                  <p class="q-px-md q-my-xs text-h5">{{ complex.name }}</p>
                  <p class="q-my-xs"><span class="q-px-md text-grey">подходящих объектов: </span><span class="text-bold">{{ complex.flats.length }}</span></p>
                  <q-virtual-scroll
                    style="max-height: 300px;"
                    :items="complex.flats"
                     separator
                    v-slot="{ item, index }"
                  >
                    <q-item
                      :key="item.id"
                      dense
                    >
                      <q-item-section>
                        <q-item-label>
                          Квартира №{{ item.number }}
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-virtual-scroll>
                  -->
                </template>
              </YandexMarker>
            </YandexClusterer>
          </template>
        </YandexMap>
      </template>
      <Loading v-else />
    </template>

    <template v-slot:right-drawer>
      <FilterConfirmDialog action="map" searchType="MapFlatSearch" />

      <div class="q-pa-md">
        <AdvancedFlatFilter
          :searchModel="searchModel.MapFlatSearch"
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
import Loading from "@/Components/Elements/Loading.vue"
import { yaMapsSettings } from '@/configurations/custom-configs'
import { YandexMap, YandexMarker, YandexClusterer } from 'vue-yandex-maps'
import ParamPair from '@/Components/Elements/ParamPair.vue'
import AdvancedFlatFilter from '@/Pages/Main/partials/AdvancedFlatFilter.vue'
import FilterConfirmDialog from '@/Pages/Main/partials/FilterConfirmDialog.vue'

export default {
  components: {
    MainLayout, Loading, YandexMap, YandexMarker, YandexClusterer, ParamPair, AdvancedFlatFilter, FilterConfirmDialog
  },
  props: {
    searchModel: {
      type: Object,
      default: {}
    },
    complexes: {
      type: Array,
      default: []
    },
    selectedCity: {
      type: Object,
      default: {}
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
  setup (props) {

    const initCoords = computed(() => {
      let latitude = 39.2112
      let longitude = 51.6708
      if (('latitude' in props.selectedCity && props.selectedCity.latitude) && ('longitude' in props.selectedCity && props.selectedCity.longitude)) {
        latitude = parseFloat(props.selectedCity.latitude)
        longitude = parseFloat(props.selectedCity.longitude)
      }
      return [longitude, latitude]
    })

    const goToComplex = (complexId) => {
      Inertia.get('/newbuilding-complex/view', {id: complexId})
    }

    const adjustBaloonHeight = () => {
      console.log('adjusting HEIGHT')
    }

    // Old variant of search: emmidiatly on filter change
    /*const emitter = useEmitter()
    emitter.on('flat-filter-changed', payload => {
      Inertia.get('/site/map', { MapFlatSearch: payload, city: payload.city_id }, { preserveState: true })
    })*/

    return {
      yaMapsSettings,
      initCoords,
      goToComplex,
      adjustBaloonHeight
    }
  }
}
</script>

<style>
.yandex-container {
  width: 100%;
  height: 100vh !important;
  margin-top: -35px;
}
.yandex-balloon {
  width: 300px;
  min-height: 400px;
}
</style>

<style scoped>
.text-ellipsis {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.baloon-complex-image {
  height: 150px;
}
.baloon-complex-logo {
  height: 30px;
}
</style>