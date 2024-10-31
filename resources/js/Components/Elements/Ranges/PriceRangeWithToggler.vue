<template>
  <div class="row justify-center">
    <div class="col-12 text-center self-center">
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

  <template v-if="inputs">
    <div class="row q-mt-sm">
      <div class="col-6 q-pr-xs">
        <q-input outlined v-model="model.min" label="От" dense>
          <template v-slot:prepend>
            <q-icon
              v-if="
                (toggleModel === '0' && model.min != '' && model.min != rangeEdges.all.min)
                || (toggleModel === '1' && model.min != '' && model.min != rangeEdges.m2.min)
              "
              size="xs"
              class="cursor-pointer"
              name="refresh"
              @click.stop.prevent="toggleModel === '0' ? model.min = rangeEdges.all.min : model.min = rangeEdges.m2.min"
            />
          </template>
          <template v-slot:append>
            <q-icon
              v-if="model.min !== null"
              size="xs"
              class="cursor-pointer"
              name="clear"
              @click.stop.prevent="model.min = null"
            />
          </template>
        </q-input>
      </div>
      <div class="col-6 q-pl-xs">
        <q-input outlined v-model="model.max" label="До" dense>
          <template v-slot:prepend>
            <q-icon
              v-if="
                (toggleModel === '0' && model.max != '' && model.max != rangeEdges.all.max)
                || (toggleModel === '1' && model.max != '' && model.max != rangeEdges.m2.max)
              "
              size="xs"
              class="cursor-pointer"
              name="refresh"
              @click.stop.prevent="toggleModel === '0' ? model.max = rangeEdges.all.max : model.max = rangeEdges.m2.max"
            />
          </template>
          <template v-slot:append>
            <q-icon
              v-if="model.max !== null"
              size="xs"
              class="cursor-pointer"
              name="clear"
              @click.stop.prevent="model.max = null"
            />
          </template>
        </q-input>
      </div>
    </div>
  </template>

  <div class="row" :class="{ 'q-mt-lg': !inputs, 'justify-between': !inputs }">
    <div :class="[inputs ? 'col-12' : 'col-10']">
      <q-range
        :model-value="model"
        @change="val => { model = val }"
        :min="toggleModel === '1' ? rangeEdges.m2.min : rangeEdges.all.min"
        :max="toggleModel === '1' ? rangeEdges.m2.max : rangeEdges.all.max"
        label
      />
    </div>
    <div v-if="!inputs" class="col-1">
      <q-btn size="xs" unelevated round icon="refresh" @click="resetModel" />
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    inputs: {
      type: Boolean,
      default: false
    },
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
    watch (model, () => { emitter.emit('price-changed', model.value) }, { deep: true })
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