<template>
  <div class="full-width text-right">
    <q-btn-toggle
      v-model="mode"
      outline
      toggle-color="primary"
      :options="[
        {value: true, slot: 'one'},
        {value: false, slot: 'two'},
      ]"
    >
      <template v-slot:one>
        <q-icon name="grid_view" />
      </template>

      <template v-slot:two>
        <q-icon name="table_view" />
      </template>
    </q-btn-toggle>
  </div>
</template>

<script>
import { ref, watch } from 'vue'
import useEmitter from '../../composables/use-emitter'

export default {
  props: ['defaultMode'],
  setup (props, {emit}) {
    const mode = ref(props.defaultMode)
    const emitter = useEmitter()
    watch(mode, () => emitter.emit('toggle-grid-table', mode.value))

    return { mode }
  }
}
</script>
