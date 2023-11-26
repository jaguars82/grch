<template>
  <div class="relative-position full-height">
    <div v-if="bacgroundImgIsLoading" class="row full-height items-center justify-center">
      <q-spinner
        color="primary"
        size="5em"
      />
    </div>
    <img class="fit" :src="`/uploads/${floorLayout}`" @load="bacgroundImgIsLoading = false" alt=""/>
    <svg
      v-if="bacgroundImgIsLoading === false"
      class="absolute-top-left fit"
      xmlns="http://www.w3.org/2000/svg"
      xmlns:xlink="http://www.w3.org/1999/xlink"
      version="1.1" x="0px" y="0px"
      :viewBox="viewBox"
    >
        <polygon
          v-if="selfCoords"
          class="flat-polygon-main"
          :points="selfCoords"
        >
        </polygon>
        <template v-if="neighboringFlats.length">
          <a v-for="nFlat of neighboringFlats"
            href="#"
            class="cursor-pointer"
            @click.prevent="goToFlat(nFlat.id)"
          >
            <polygon
              class="flat-polygon"
              :points="nFlat.layout_coords"
            >
            </polygon>
          </a>
        </template>
    </svg>
  </div>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    floorLayout: {
      type: String
    },
    selfCoords: {
      type: String
    },
    viewBox: {
      type: String
    },
    neighboringFlats: {
      neighboringFlats: {
        type: Array,
        default: []
      }
    }
  },
  setup () {
    const bacgroundImgIsLoading = ref(true)
    const goToFlat = function (flatId) {
      Inertia.get('/flat/view', { id: flatId })
    }
    return { bacgroundImgIsLoading, goToFlat }
  }
}
</script>

<style scoped>
.flat-polygon {
  fill: transparent;
}
.flat-polygon:hover {
  fill: rgba(51, 122, 183, 0.3);
}
.flat-polygon-main {
  fill: rgba(0, 128, 1, 0.3);
}
</style>