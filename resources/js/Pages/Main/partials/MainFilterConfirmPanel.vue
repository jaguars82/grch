<template>
  <div class="column">
    <div class="row">
      <q-btn color="primary" :size="$q.screen.sm ? 'sm' : 'md'" class="text-white q-ml-sm q-mr-xs" unelevated round icon="search" @click="search" :disabled="!newSearchPrecount" />
      <q-btn color="white" :size="$q.screen.sm ? 'sm' : 'md'" class="text-grey-7" unelevated round icon="pin_drop" @click="mapSearch" :disabled="!newSearchPrecount" />
    </div>
    <div class="q-ml-sm" v-if="presearchHasBeenProcessed">
      <q-chip color="orange" text-color="white" size="sm">Объектов: <strong>{{ newSearchPrecount }}</strong></q-chip>
    </div>
  </div>
</template>

<script>
import { ref, watch, nextTick } from 'vue'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    formState: Object
  },
  setup (props) {
    const presearchHasBeenProcessed = ref(false)
    const newSearchPrecount = ref(0)

    watch (() => props.formState, async (newState) => {
      try {
        const response = await axios.post('/site/pre-search', { AdvancedFlatSearch: newState })
        newSearchPrecount.value = response.data
        presearchHasBeenProcessed.value = true
        nextTick(() => {
          // Render after all the data renewal
        })
      } catch (error) {
        console.log(error)
      }
    }, { deep: true })

    const search = () => {
      Inertia.get(`site/search`, { AdvancedFlatSearch: props.formState })
    }
    const mapSearch = () => {
      Inertia.get(`site/map`, { MapFlatSearch: props.formState })
    }

    return { presearchHasBeenProcessed, newSearchPrecount, search, mapSearch } 
  }
}
</script>