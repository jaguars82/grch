<template>
  <q-card flat>

    <q-card-section>
      <p class="text-h4 q-mb-xs"><span class="text-capitalize">{{ flatRoomTitle }}</span> квартира № {{ flat.number }}, {{ flatArea }}</p>
      <p>{{ flat.newbuildingComplex.name }} > {{ flat.newbuilding.name }} > {{ flatFloor }}, сдача: {{ flatDeadline }}</p>
      <p class="text-h2 text-bold text-blue-8 q-mb-xs">{{ flatPriceCash }}</p>
      <p class="text-grey-7">{{ flatPricePerMeter }}</p>
    </q-card-section>

    <q-card-section v-if="viewOptions.group.show" class="q-px-md q-py-xs">
      <div class="row">
        <div v-if="viewOptions.group.flat" class="col-12 text-center">
          <div class="image-frame rounded-borders q-px-sm q-py-md">
          <p class="text-h4 text-center">Планировка квартиры</p>
            <div class="image-aligner">
              <img
                class="flatImage"
                ref="flatImage"
                v-if="flat.layout"
                :src="`/uploads/${flat.layout}`"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="row q-col-gutter-none">
        <div v-if="viewOptions.group.floor" class="col-12 col-md-6 q-mt-md q-pr-none">
          <div class="image-frame rounded-borders q-px-sm q-py-md" :class="{ 'q-mr-sm': $q.screen.gt.sm }">
            <p class="text-h4 text-center">План этажа</p>
            <div class="image-aligner small">
              <img
                class="floorImage"
                :src="`/uploads/floorlayout-selections/${flat.floorLayoutImage}`"
              />
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 q-mt-md q-pr-none">
          <div class="image-frame rounded-borders q-px-sm q-py-md" :class="{ 'q-ml-sm': $q.screen.gt.sm }">
            <p class="text-h4 text-center">Генплан</p>
            <div class="image-aligner small">
              <img
                class="genplan-image"
                v-if="flat.layout"
                :src="`/uploads/${flat.newbuildingComplex.master_plan}`"
              />
            </div>
          </div>
        </div>
      </div>
    </q-card-section>

    <q-card-section v-if="flat.newbuildingComplex.longitude && flat.newbuildingComplex.latitude">

      <p class="text-h4 q-mb-xs">На карте</p>
      
      <YandexMap
        :settings="yaMapsSettings"
        :coordinates="[flat.newbuildingComplex.longitude, flat.newbuildingComplex.latitude]"
        :zoom="16"
      >
        <YandexMarker
          marker-id="1"
          type="Point"
          :coordinates="[flat.newbuildingComplex.longitude, flat.newbuildingComplex.latitude]"
        ></YandexMarker>
      </YandexMap>

    </q-card-section>  
  </q-card>
</template>

<script>

import { ref, computed, onMounted } from 'vue'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea } from '@/helpers/formatter'
import { yaMapsSettings } from '@/configurations/custom-configs'
import { YandexMap, YandexMarker } from 'vue-yandex-maps'

export default ({
  props: {
    flat: Object,
    configuration: Object
  },
  components: {
    YandexMap, YandexMarker
  },
  setup(props) {

    const viewOptions = computed(()=>{
      const options = props.configuration
        ? props.configuration
        : {
          groupLayouts: false
        }
      return options
    })

    const flatRoomTitle = computed(() => `${asNumberString(props.flat.rooms)}комнатная`)
    const flatFloor = computed(() => asFloor(props.flat.floor, props.flat.newbuilding.total_floor))
    const flatArea = computed(() => asArea(props.flat.area))
    const flatPriceCash = computed(() => asCurrency(props.flat.price_cash))
    const flatPricePerMeter = computed(() => asPricePerArea(props.flat.price_cash / props.flat.area))
    const flatDeadline = computed(() => !props.flat.newbuilding.deadline ? 'нет данных' : new Date() > new Date(props.flat.newbuilding.deadline) ? 'позиция сдана' : asQuarterAndYearDate(props.flat.newbuilding.deadline))

    const flatImage = ref(null)

    return { viewOptions, flatArea, flatFloor, flatPriceCash, flatRoomTitle, flatPricePerMeter, flatDeadline, flatImage, /*floorLayoutImage,*/ yaMapsSettings }
    
  },
})
</script>

<style>
.yandex-container {
  width: 100%;
  height: 300px!important;
  margin-top: -35px;
}

.image-frame {
  height: 100%;
  border-style: solid;
  border-width: thin;
  border-color: grey;
}

.image-aligner {
  display: flex;
  justify-content: center;
  align-items: center;
}

.image-aligner.small {
  height: 350px;
}

.flatImage {
  width: 100%;
  max-height: 450px;
}

.floorImage,
.genplan-image {
  width: 100%;
  max-height: 350px;
}

</style>