<template>
  <div v-if="header" class="row items-start">
    <div class="col">
      <q-avatar :size="$q.screen.lt.md ? 'lg' : 'xl'" icon="location_on" color="primary"/>
    </div>
    <div :class="{ 'col-11': $q.screen.gt.xs, 'col-10': $q.screen.lt.sm }">
      <TitleSubtitle title="На карте" :subtitle="address"/>
    </div>
  </div>
  <YandexMap
    :settings="yaMapsSettings"
    :coordinates="mapCoordinates"
    :options="{ autoFitToViewport: 'always' }"
    :zoom="16"
  >
    <template v-if="markers.length">
      <YandexClusterer>
        <YandexMarker
          v-for="(marker, index) of markers"
          :marker-id="index + 1"
          type="Point"
          :coordinates="reverseMarkerCoords ? [marker.longitude, marker.latitude] : [marker.latitude, marker.longitude]"
        >
        </YandexMarker>
      </YandexClusterer>
    </template>
  </YandexMap>
</template>

<script>
import { computed } from 'vue'
import TitleSubtitle from '@/Components/Elements/TitleSubtitle.vue'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { YandexMap, YandexMarker, YandexClusterer } from 'vue-yandex-maps'

export default {
  components: {
    TitleSubtitle, YandexMap, YandexMarker, YandexClusterer
  },
  props: {
    header: {
      type: Boolean,
      default: true
    },
    address: {
      type: String,
      default: ''
    },
    latitude: {
      type: [Number, String],
      default: 51.6708,
    },
    longitude: {
      type: [Number, String],
      default: 39.2112,
    },
    markers: {
      type: Array,
      default: []
    },
    reverseCoords: {
      type: Boolean,
      default: false
    },
    reverseMarkerCoords: {
      type: Boolean,
      default: false
    }
  },
  setup (props) {
    const mapCoordinates = computed (() => {
      const latitude = typeof props.latitude === 'string' ? parseFloat(props.latitude) : props.latitude
      const longitude = typeof props.longitude === 'string' ? parseFloat(props.longitude) : props.longitude
      return props.reverseCoords ? [longitude, latitude] : [latitude, longitude]
    })
    return { yaMapsSettings, mapCoordinates }
  }
}
</script>

<style scoped>
.yandex-container {
  width: 100%;
  height: 450px !important;
  margin-top: -35px;
}
</style>