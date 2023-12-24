<template>
  <q-dialog v-model="dialog">
    <q-card
      :position="position"
      class="text-white"
      :class="{
        'bg-primary': messageType === 'info',
        'bg-positive': messageType === 'success',
        'bg-negative': messageType === 'danger',
        'bg-warning': messageType === 'warn',
      }"
    >
      <q-card-section>
        <div class="col-11">
          {{ messageText }}
        </div>
        <div class="col-1">
          <q-btn round unelevated size="xs" dense icon="close" v-close-popup/>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import { ref } from 'vue'

export default {
  props: {
    open: {
      type: Boolean,
      default: false
    },
    messageType: {
      type: String,
      default: 'info' // possible values: 'info', 'success', 'warn', 'danger'
    },
    position: {
      type: String,
      default: 'top'
    },
    messageText: {
      type: String,
      default: ''
    }
  },
  setup (props) {
    const dialog = ref(props.open)
    return { dialog }
  }
}
</script>