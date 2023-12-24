<template>
  <q-btn
    class="q-py-sm"
    rounded
    unelevated
    color="red"
    @click="goToReserve"
    icon="lock"
    label="Бронировать"
  >
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
    const goToReserve = () => {
      Inertia.get('/reservation/make', { flatId: props.flat.id })
    }
    return { goToReserve }
  }
}
</script>