<template>
  <div class="q-py-sm">
    <q-btn-dropdown flat rounded>
      <q-list>
        <q-item v-for="region of regionOptions" clickable v-close-popup @click="onItemClick(region)">
          <q-item-section>
            <q-item-label>{{ region.label }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
      <template v-slot:label>
        <q-avatar size="sm" class="text-orange q-mr-sm" icon="location_on" color="white" />
        <span>{{ regionSelect.label }}</span>
      </template>
    </q-btn-dropdown>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import useEmitter from '@/composables/use-emitter'
import { idNameObjToOptions, selectOneFromOptionsList } from '@/composables/formatted-and-processed-data'

export default {
  props: {
    regions: Object,
    regionId: {
      type: Number,
      default: 1
    }
  },
  setup (props) {
    const regionOptions = computed(() => idNameObjToOptions(props.regions))
    const initRegionSelect = computed(() => selectOneFromOptionsList(regionOptions.value, props.regionId))
    const regionSelect = ref(initRegionSelect.value)

    const emitter = useEmitter()
    
    const emitRegion = () => {
      emitter.emit('region-changed', regionSelect.value.value)
    }

    const onItemClick = (region) => {
      regionSelect.value = region
      emitRegion()
    }

    onMounted (() => {
      emitRegion()
    })

    return {
      regionOptions,
      initRegionSelect,
      regionSelect,
      onItemClick
    }
  }
}
</script>