<template>
  <q-card class="no-shadow">
    <q-card-section>
      <p class="text-h4 q-mb-xs"><span class="text-capitalize">{{ flatRoomTitle }}</span> квартира № {{ flat.number }}, {{ flatArea }}</p>
      <p>{{ flat.newbuildingComplex.name }} > {{ flat.newbuilding.name }} > {{ flatFloor }}, сдача: {{ flatDeadline }}</p>
      <p class="text-h2 text-bold text-blue-8 q-mb-xs">{{ flatPriceCash }}</p>
      <p class="text-grey-7">{{ flatPricePerMeter }}</p>
    </q-card-section>

    <q-card-section class="q-px-md q-py-xs">
      <img ref="floorImage" v-if="flat.layout"
        :src="`/uploads/${flat.layout}`"
      />
      <div class="no-pointer-events" v-if="flat.floorLayoutImage" v-html="flat.floorLayoutImage"></div>
      <!--<img :src="floorLayoutImage" />-->
      <q-btn label="check" @click="onCheck" />
    </q-card-section>

    <q-card-section v-if="flat.newbuildingComplex.longitude && flat.newbuildingComplex.latitude">
      
      <yandex-map
        :settings="yaMapsSettings"
        :coords="[flat.newbuildingComplex.longitude, flat.newbuildingComplex.latitude]"
        zoom="16"
        ymap-class="ya-map-container"
      >
        <ymap-marker
          marker-id="1"
          marker-type="placemark"
          :coords="[flat.newbuildingComplex.longitude, flat.newbuildingComplex.latitude]"
          hint-content="Hint content 1"
          :balloon="{header: 'header', body: 'body', footer: 'footer'}"
          :icon="{color: 'green'}"
          cluster-name="1"
        ></ymap-marker>
      </yandex-map>

    </q-card-section>  
  </q-card>
</template>

<script>

import { ref, computed } from 'vue'
//import svg64 from 'svg64'
import html2canvas from 'html2canvas'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea } from '@/helpers/formatter'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { yandexMap, ymapMarker } from 'vue-yandex-maps'

export default ({
  props: {
    flat: Object
  },
  components: {
    yandexMap,
    ymapMarker
  },
  setup(props) {

    const flatRoomTitle = computed(() => `${asNumberString(props.flat.rooms)}комнатная`)
    const flatFloor = computed(()=> asFloor(props.flat.floor, props.flat.newbuilding.total_floor))
    const flatArea = computed(() => asArea(props.flat.area))
    const flatPriceCash = computed(() => asCurrency(props.flat.price_cash))
    const flatPricePerMeter = computed(() => asPricePerArea(props.flat.price_cash / props.flat.area))
    const flatDeadline = computed(() => !props.flat.newbuilding.deadline ? 'нет данных' : new Date() > new Date(props.flat.newbuilding.deadline) ? 'позиция сдана' : asQuarterAndYearDate(props.flat.newbuilding.deadline))
    //const floorLayoutImage = computed( () => props.flat.floorLayoutImage ? svg64(props.flat.floorLayoutImage) : false )
    //const floorLayoutImage = computed( () => props.flat.floorLayoutImage ? "data:image/svg+xml;base64," + btoa(unescape(encodeURIComponent(props.flat.floorLayoutImage))) : false )

    const floorImage = ref(null)

    const onCheck = function () {
      html2canvas(floorImage.value).then(canvas => {
          document.body.appendChild(canvas)
          console.log(canvas)
      })
      //console.log(floorImage.value)
    }

    return { flatArea, flatFloor, flatPriceCash, flatRoomTitle, flatPricePerMeter, flatDeadline, floorImage, onCheck, /*floorLayoutImage,*/ yaMapsSettings }
    
  },
})
</script>

<style>
.ya-map-container {
  width: 100%;
  height: 300px!important;
}
</style>