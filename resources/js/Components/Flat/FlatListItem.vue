<template>
  <q-card class="shadow-7">
    <q-card-section class="q-px-md q-py-xs" horizontal>
      <div class="col-4 self-center">
        <q-img v-if="flat.layout"
          fit="scale-down"
          :src="`/uploads/${flat.layout}`"
        />
        <q-img v-else fit="scale-down" src="/img/flat.png" />
      </div>
      <q-card-section>
        <a :href="`/flat/view?id=${flat.id}`">
          <p class="text-h4 q-mb-xs"><span class="text-capitalize">{{ flatRoomTitle }}</span> квартира № {{ flat.number }}</p>
        </a>
        <p class="q-mb-xs">{{ flatArea }}, {{ flatFloor }}, сдача: {{ flatDeadline }}</p>
        <p class="text-bold">{{ flat.developer.name }} > {{ flat.newbuildingComplex.name }} > {{ flat.newbuilding.name }}</p>
        <p class="text-h2 text-bold text-blue-8 q-mb-xs">{{ flatPriceCash }}</p>
        <p class="text-grey-7">{{ flatPricePerMeter }}</p>
        <a :href="`/newbuilding-complex/view?id=${flat.newbuildingComplex.id}`">
        <!--<inertia-link :href="`/newbuilding-complex/view?id=${flat.newbuildingComplex.id}`">-->
          <div v-if="flat.newbuildingComplex.logo" class="nbc-logo-container">
            <q-img fit="scale-down" :src="`/uploads/${flat.newbuildingComplex.logo}`" />
          </div>
          <p v-else class="text-h5">{{ flat.newbuildingComplex.name }}</p>
        <!--</inertia-link>-->
        </a>
      </q-card-section>
    </q-card-section>
  </q-card>
</template>

<script>

import { computed } from 'vue'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea } from '../../helpers/formatter'

export default ({
  props: {
    flat: Object
  },
  setup(props) {

    const flatRoomTitle = computed(() => `${asNumberString(props.flat.rooms)}комнатная`)
    const flatFloor = computed(()=> asFloor(props.flat.floor, props.flat.newbuilding.total_floor))
    const flatArea = computed(() => asArea(props.flat.area))
    const flatPriceCash = computed(() => asCurrency(props.flat.price_cash))
    const flatPricePerMeter = computed(() => asPricePerArea(props.flat.price_cash / props.flat.area))
    const flatDeadline = computed(() => !props.flat.newbuilding.deadline ? 'нет данных' : new Date() > new Date(props.flat.newbuilding.deadline) ? 'позиция сдана' : asQuarterAndYearDate(props.flat.newbuilding.deadline))

    return { flatArea, flatFloor, flatPriceCash, flatRoomTitle, flatPricePerMeter, flatDeadline }
    
  },
})
</script>

<style scoped>
.nbc-logo-container {
  display: flex;
  justify-content: center;
  width: 80px;
  height: 80px;
}
</style>