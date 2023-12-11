<template>
  <h5 v-if="title">{{ title }}</h5>
  <div class="row">
    <div class="col-6 q-pr-xs">
      <q-input outlined v-model="rangeValue.min" label="От" dense>
        <template v-slot:prepend>
          <q-icon
            v-if="rangeValue.min != '' && rangeValue.min != edges.min"
            size="xs"
            class="cursor-pointer"
            name="refresh"
            @click.stop.prevent="rangeValue.min = edges.min"
          />
        </template>
        <template v-slot:append>
          <q-icon
            v-if="rangeValue.min !== null"
            size="xs"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="rangeValue.min = null"
          />
        </template>
      </q-input>
    </div>
    <div class="col-6 q-pl-xs">
      <q-input outlined v-model="rangeValue.max" label="До" dense>
        <template v-slot:prepend>
          <q-icon
            v-if="rangeValue.max != '' && rangeValue.max != edges.max"
            size="xs"
            class="cursor-pointer"
            name="refresh"
            @click.stop.prevent="rangeValue.max = edges.max"
          />
        </template>
        <template v-slot:append>
          <q-icon
            v-if="rangeValue.max !== null"
            size="xs"
            class="cursor-pointer"
            name="clear"
            @click.stop.prevent="rangeValue.max = null"
          />
        </template>
      </q-input>
    </div>
  </div>
  <q-range
    :model-value="rangeValue"
    @change="val => { rangeValue = val }"
    :min="parseInt(edges.min)"
    :max="parseInt(edges.max)"
    label
  />
</template>

<script>
  import { ref, watch } from 'vue'
  import useEmitter from '@/composables/use-emitter'

  export default {
    props: {
      name: {
        type: String,
        default: "" // must provide name for component to emit change event
      },
      title: {
        type: String,
        default: ""
      },
      model: {
        type: Object,
        default: { min: null, max: null }
      },
      edges: {
        type: Object,
        default: { min: null, max: null }
      },
    },
    setup (props) {
      const rangeValue = ref({ min: props.model.min, max: props.model.max })

      const emitter = useEmitter()

      watch (rangeValue, () => {
        if (props.name) {
          emitter.emit(`${props.name}-range-changed`, rangeValue.value)
        } else {
          console.log('range component needs the "name" prop to be provided to emit the "change" event')
        }
      })

      return { rangeValue }
    }
  }
</script>