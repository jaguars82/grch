<template>
  <!--<div class="column">
    <div class="row">
      <q-btn color="primary" :size="$q.screen.sm ? 'sm' : 'md'" class="text-white q-ml-sm q-mr-xs" unelevated round icon="search" @click="search" :disabled="!newSearchPrecount" />
      <q-btn color="white" :size="$q.screen.sm ? 'sm' : 'md'" class="text-grey-7" unelevated round icon="pin_drop" @click="mapSearch" :disabled="!newSearchPrecount" />
    </div>
    <div class="q-ml-sm" v-if="presearchHasBeenProcessed">
      <q-chip color="orange" text-color="white" size="sm">Объектов: <strong>{{ newSearchPrecount }}</strong></q-chip>
    </div>
  </div>-->
  <div class="column q-ml-xs q-pa-xs container-bg rounded-left rounded-right">
    <div class="row no-wrap justify-end items-center">
      <div class="q-ml-sm self-center" v-if="presearchHasBeenProcessed">
        <span class="text-white text-h4" v-if="$q.screen.xs">Объектов: </span>
        <span class="text-white text-h4 text-bold" :class="{'non-selectable': $q.screen.gt.xs}">
        <q-tooltip v-if="$q.screen.gt.xs">
          Количество объектов
        </q-tooltip>
          {{ newSearchPrecount }}
        </span>
      </div>
      <q-btn color="primary" size="sm" class="text-white q-ml-sm q-mr-xs" unelevated round icon="search" @click="search" :disabled="!newSearchPrecount">
        <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
          Показать объекты
        </q-tooltip>
      </q-btn>
      <q-btn color="white" size="sm" class="text-grey-7" unelevated round icon="pin_drop" @click="mapSearch" :disabled="!newSearchPrecount">
      <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
          ЖК на карте
        </q-tooltip>
      </q-btn>
    </div>
    <!--<div class="q-ml-sm" v-if="presearchHasBeenProcessed">
      <span class="text-white">Объектов: <strong>{{ newSearchPrecount }}</strong></span>
    </div>-->
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

<style scoped>
.container-bg {
  background-color: rgba(255, 152, 0, 0.75);
  /*border: solid thin white;*/
}
.rounded-left {
  border-top-left-radius: 20px !important;
  border-bottom-left-radius: 20px !important;
}
.rounded-right {
  border-top-right-radius: 20px !important;
  border-bottom-right-radius: 20px !important;
}
</style>