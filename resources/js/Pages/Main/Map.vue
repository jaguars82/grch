<template>
  <MainLayout>
    <template v-slot:main>
      <YandexMap
        :settings="yaMapsSettings"
        :coordinates="initCoords"
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
                <div v-for="flat of complex.flats">{{ flat.id }} = #{{ flat.number }}</div>
              </template>
            </YandexMarker>
          </YandexClusterer>
        </template>
      </YandexMap>
      <pre>{{ complexes }}</pre>
      <pre>{{ selectedCity }}</pre>
      <pre>{{ initCoords }}</pre>
    </template>
  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { YandexMap, YandexMarker, YandexClusterer } from 'vue-yandex-maps'

export default {
  components: {
    MainLayout, YandexMap, YandexMarker, YandexClusterer
  },
  props: {
    selectedCity: {
      type: Object,
      default: {}
    },
    complexes: {
      type: Array,
      default: []
    },
    positionArray: {
      type: Object,
      default: {}
    }
  },
  setup (props) {

    const initCoords = computed(() => {
      let latitude = 39.2112
      let longitude = 51.6708
      if (('latitude' in props.selectedCity && props.selectedCity.latitude) && ('longitude' in props.selectedCity && props.selectedCity.longitude)) {
        latitude = props.selectedCity.latitude
        longitude = props.selectedCity.longitude
      }
      return [longitude, latitude]
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
</style>