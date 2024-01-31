<template>
  <q-card class="flat-card shadow-7">
    <q-card-section class="q-py-xs" :class="{ 'q-px-xs': $q.screen.xs, 'q-px-md': $q.screen.gt.xs }" :horizontal="$q.screen.gt.xs">
      <div v-if="$q.screen.gt.xs" class="col-4 self-center">
        <q-img v-if="flat.layout"
          class="flat-img"
          fit="contain"
          :src="`/uploads/${flat.layout}`"
        />
        <q-img v-else fit="contain" src="/img/flat.png" />
      </div>
      <q-card-section :class="{ 'q-pa-sm': $q.screen.xs, 'q-pa-md': $q.screen.gt.xs }">
        <q-img
          v-if="$q.screen.lt.sm && flat.layout"
          class="flat-img q-mb-sm"
          fit="contain"
          :src="flat.layout ? `/uploads/${flat.layout}` : '/img/flat.png'"
        />
        <inertia-link :href="`/flat/view?id=${flat.id}`">
          <p class="q-mb-xs" :class="{ 'text-h4': $q.screen.lt.md, 'text-h3': $q.screen.gt.sm }"><span class="text-capitalize">{{ flatRoomTitle }}</span> квартира № {{ flat.number }}</p>
        </inertia-link>
        <p class="q-mb-xs">{{ flatArea }}, {{ flatFloor }}, сдача: {{ flatDeadline }}</p>
        <p class="text-bold">
          {{ flat.developer.name }} > {{ flat.newbuildingComplex.name }} > {{ flat.newbuilding.name }}
          <span v-if="flat.entrance.name"> > {{ flat.entrance.name }}</span>
        </p>
        <p 
          class="text-bold text-blue-8 q-mb-xs"
          :class="{ 'text-h4': $q.screen.xs, 'text-h3': $q.screen.sm, 'text-h2': $q.screen.gt.sm }"
        >
          {{ flatPriceCash }}
        </p>
        <p class="text-grey-7">{{ flatPricePerMeter }}</p>
        <inertia-link :href="`/newbuilding-complex/view?id=${flat.newbuildingComplex.id}`">
        </inertia-link>
        <NewbuildingComplexBadge
          :id="flat.newbuildingComplex.id"
          :logo="flat.newbuildingComplex.logo"
          :name="flat.newbuildingComplex.name"
          :address="flat.newbuildingComplex.address"
        />
      </q-card-section>
    </q-card-section>
  </q-card>
</template>

<script>

import { computed } from 'vue'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea } from '../../helpers/formatter'
import NewbuildingComplexBadge from '@/Components/NewbuildingComplex/NewbuildingComplexBadge.vue'

export default ({
  props: {
    flat: Object
  },
  components: { NewbuildingComplexBadge },
  setup(props) {

    const flatRoomTitle = computed(() => `${asNumberString(props.flat.rooms)}комнатная`)
    const flatFloor = computed(() => asFloor(props.flat.floor, props.flat.newbuilding.total_floor))
    const flatArea = computed(() => asArea(props.flat.area))
    const flatPriceCash = computed(() => asCurrency(props.flat.price_cash))
    const flatPricePerMeter = computed(() => asPricePerArea(props.flat.price_cash / props.flat.area))
    const flatDeadline = computed(() => !props.flat.newbuilding.deadline ? 'нет данных' : new Date() > new Date(props.flat.newbuilding.deadline) ? 'позиция сдана' : asQuarterAndYearDate(props.flat.newbuilding.deadline))

    return { flatArea, flatFloor, flatPriceCash, flatRoomTitle, flatPricePerMeter, flatDeadline }
    
  },
})
</script>

<style scoped>
.flat-card .flat-img {
  max-height: 350px;
}
</style>