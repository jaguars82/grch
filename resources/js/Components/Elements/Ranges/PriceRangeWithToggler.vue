<template>
  <div class="row">
    <div class="col-12">
      <q-btn-toggle
        unelevated
        v-model="toggleModel"
        toggle-color="primary"
        :options="[
          { label: 'За всё', value: '0' },
          { label: 'За м²', value: '1' },
        ]"
        @update:model-value="resetModel"
      />
    </div>
  </div>
  <div class="q-mt-lg row justify-between">
    <div class="col-10">
      <q-range
        :model-value="model"
        @change="val => { model = val }"
        :min="toggleModel === '1' ? rangeEdges.m2.min : rangeEdges.all.min"
        :max="toggleModel === '1' ? rangeEdges.m2.max : rangeEdges.all.max"
        label
      />
    </div>
    <div class="col-1">
      <q-btn size="xs" unelevated round icon="refresh" @click="resetModel" />
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    priceRange: {
      type: Object,
      default: { min: null, max: null }
    },
    priceType: {
      type: [String, Number],
      default: '0'
    },
    rangeEdges: {
      type: Object,
      default: {
        all: { min: 0, max: 0 },
        m2: { min: 0, max: 0 }
      }
    }
  },
  setup (props) {
    const toggleModel = ref(props.priceType)
    const model = ref(props.priceRange)
    const resetModel = () => {
      model.value = { min: null, max: null }
    }
    const emitter = useEmitter()
    watch (model, () => emitter.emit('price-changed', model.value))
    watch (toggleModel, () => emitter.emit('price-type-changed', toggleModel.value))
    return { model, toggleModel, resetModel }
  }
}
</script>

<style scoped>
.row.justify-between::before, .row.justify-between::after {
  display: none;
}
</style>