<template>
  <MainLayout :gutters="false" :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: true } }">
    <template v-slot:main>
      <YandexMap
        :settings="yaMapsSettings"
        :coordinates="initCoords"
        :options="{ autoFitToViewport: 'always' }"
        :zoom="12"
      >
        <template v-if="complexes.length">
          <YandexClusterer>
            <YandexMarker
              v-for="complex of complexes"
              :marker-id="complex.id"
              type="Point"
              :coordinates="[complex.longitude, complex.latitude]"
            >
              <template #component>
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
              </template>
            </YandexMarker>
          </YandexClusterer>
        </template>
      </YandexMap>
    </template>

    <template v-slot:right-drawer>
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
import { yaMapsSettings } from '@/configurations/custom-configs'
import { YandexMap, YandexMarker, YandexClusterer } from 'vue-yandex-maps'
import AdvancedFlatFilter from '@/Pages/Main/partials/AdvancedFlatFilter.vue'

export default {
  components: {
    MainLayout, YandexMap, YandexMarker, YandexClusterer, AdvancedFlatFilter
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

    const emitter = useEmitter()
    emitter.on('flat-filter-changed', payload => {
      Inertia.get('/site/map', { MapFlatSearch: payload, city: payload.city_id }, { preserveState: true })
    })

    return {
      yaMapsSettings,
      initCoords
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
  height: 200px;
}
</style>