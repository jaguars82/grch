<template>
  <div
    class="column q-ma-xs q-pa-xs cursor-pointer rounded-borders bg-white flat-cell"
    :class="{ 'bg-blue-2': currentlyOpened }"
    @click="goToFlat(flat.id)"
    @mouseenter="focusOn"
    @mouseleave="focusOff"
  >
    <div class="row justify-between">
      <q-badge :color="color">{{ flat.rooms }}
      </q-badge>
      <q-badge color="orange" v-if="flat.status === 0 && flat.has_discount">
        Акция
      </q-badge>
      <div class="text-grey-7">№ <span class="text-weight-bolder">{{ flat.number }}</span></div>
    </div>
    <div class="q-py-sm text-bold">
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

export default {
  props: {
    flat: Object,
    currentlyOpened: {
      type: Boolean,
      default: false
    }
  },
  setup (props) {
    const color = computed(() => {
      let color = 'grey'
      switch (props.flat.status) {
        case 0:
          color = 'green'
          break
        case 1:
          color = 'orange'
          break
        case 2:
          color = 'red'
          break
      }
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
  .flat-cell {
    width: 120px;
    min-width: 120px;
  }
</style>