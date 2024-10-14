<template>
  <div
    v-if="flat === 'filler'"
    class="column q-ma-xs q-pa-xs rounded-borders bg-grey-3"
    :class="{ 'flat-cell-sm': $q.screen.xs, 'flat-cell-md': $q.screen.gt.xs }"
  ></div>
  <div
    v-else
    class="column q-ma-xs q-pa-xs cursor-pointer rounded-borders bg-white"
    :class="{ 'flat-cell-sm': $q.screen.xs, 'flat-cell-md': $q.screen.gt.xs, 'bg-blue-2': currentlyOpened }"
    @click="goToFlat(flat.id)"
    @mouseenter="focusOn"
    @mouseleave="focusOff"
  >
    <div class="row justify-between">
      <q-badge v-if="flat.is_commercial == 1" :color="color">К</q-badge>
      <q-badge v-else :color="color">{{ flat.rooms }}</q-badge>
      <q-badge class="gt-xs" color="orange" v-if="flat.status === 0 && flat.has_discount">
        <span>Акция</span>
      </q-badge>
      <div class="text-grey-7">№ <span v-if="flat.number_string" class="text-weight-bolder">{{ flat.number_string }}</span><span v-else class="text-weight-bolder">{{ flat.number }}</span></div>
    </div>
    <div class="q-py-sm" :class="{ 'text-bold': $q.screen.gt.xs, 'text-h6': $q.screen.xs }">
      <span v-if="flat.has_discount">{{ flat.price_range }}</span>
      <span v-else>{{ asCurrency(flat.price_cash) }}</span>
    </div>
    <div class="row justify-between">
      <div class="text-bold text-grey-7 text-body2">{{ asArea(flat.area) }}</div>
      <div class="text-grey text-body2">
        {{ asPricePerArea(Math.round(flat.price_cash / flat.area)) }}
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { asArea, asCurrency, asPricePerArea } from '@/helpers/formatter'
import { flatStatusColors } from '@/composables/model-configurations'

export default {
  props: {
    flat: [Object, String],
    currentlyOpened: {
      type: Boolean,
      default: false
    }
  },
  setup (props) {
    const color = computed(() => {
      let color = 'grey'
      
      if (props.flat === 'filler') return color
      
      color = flatStatusColors[props.flat.status]

      return color
    })
    
    const focusOn = function (event) {
      if (props.currentlyOpened) return
      event.target.classList.add(`bg-${color.value}-2`)
    }
    const focusOff = function (event) {
      if (props.currentlyOpened) return
      event.target.classList.remove(`bg-${color.value}-2`)
    }
    const goToFlat = function (flatId) {
      Inertia.get('/flat/view', { id: flatId })
    }

    return {
      asArea,
      asCurrency,
      asPricePerArea,
      color,
      focusOn,
      focusOff,
      goToFlat,
    }
  }
}
</script>

<style scoped>
  .row.justify-between::before, .row.justify-between::after {
    display: none;
  }
  .flat-cell-md {
    width: 120px;
    min-width: 120px;
  }
  .flat-cell-sm {
    width: 80px;
    min-width: 80px;
  }
</style>