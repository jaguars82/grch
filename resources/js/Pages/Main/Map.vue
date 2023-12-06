<template>
  <MainLayout>
    <template v-slot:main>
      <yandex-map
        :settings="yaMapsSettings"
        :coords="initCoords"
        zoom="12"
        ymap-class="ya-map-container"
      >
        <template v-if="complexes.length">
          <ymap-marker
            v-for="complex of complexes"
            :marker-id="complex.id"
            marker-type="placemark"
            :balloon-template="[
          '<ul>',
          '{% for flat in complex.flat %}',
          '<li>{{ flat.id }}</li>',
          '{% endfor %}',
          '</ul>',
        ].join('')"
            :coords="[complex.longitude, complex.latitude]"
            :icon="{color: 'green'}"
            cluster-name="1"
          >
          </ymap-marker>
        </template>
      </yandex-map>
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
import { yandexMap, ymapMarker } from 'vue-yandex-maps'

export default {
  components: {
    MainLayout, yandexMap, ymapMarker
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
.ya-map-container {
  width: 100%;
  height: 100vh !important;
  margin-top: -35px;
}
</style>