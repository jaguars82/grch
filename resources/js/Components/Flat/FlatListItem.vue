<template>
  <q-card class="shadow-7">
    <q-card-section class="q-px-md q-py-xs" horizontal>
      <q-img v-if="flat.layout"
        class="col-4"
        :src="`/uploads/${flat.layout}`"
      />
      <q-img v-else class="col-4" src="/img/flat.png" />
      <q-card-section>
        <p class="text-h4 q-mb-xs"><span class="text-capitalize">{{ flatRoomTitle }}</span> квартира № {{ flat.number }}</p>
        <p>{{ flatArea }}, {{ flatFloor }}, сдача: {{ flatDeadline }}</p>
        <p class="text-h2 text-bold text-blue-8 q-mb-xs">{{ flatPriceCash }}</p>
        <p class="text-grey-7">{{ flatPricePerMeter }}</p>
        <a :href="`/newbuilding-complex/view?id=${flat.newbuildingComplex.id}`">
        <!--<inertia-link :href="`/newbuilding-complex/view?id=${flat.newbuildingComplex.id}`">-->
          <div v-if="flat.newbuildingComplex.logo" class="nbc-logo-container">
            <q-img class="nbc-logo" :src="`/uploads/${flat.newbuildingComplex.logo}`" />
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

.nbc-logo {
  align-self: center;
  max-width: 100%;
  max-height: 100%;
}
</style>