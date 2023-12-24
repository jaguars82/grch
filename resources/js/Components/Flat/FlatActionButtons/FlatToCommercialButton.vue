<template>
  <q-btn
    round
    unelevated
    outline
    color="orange"
    @click="goToMakeCommercial"
    label="КП"
  >
    <q-tooltip :offset="[0, 10]" :delay="1000">
      Коммерческое предложение
    </q-tooltip>
  </q-btn>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    user: Object,
    flat: Object
  },
  setup (props) {
    const goToMakeCommercial = () => {
      Inertia.get('/user/commercial/make', { flatId: props.flat.id })
    }
    return { goToMakeCommercial }
  }
}
</script>