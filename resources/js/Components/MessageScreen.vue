<template>
  <q-card>
    <q-card-section>
      <q-icon :color="icon.color" :name="icon.picture" />
    </q-card-section>
  </q-card>
</template>

<script>
import { computed } from 'vue'

export default ({
  props: {
    type: {
      type: String,
      default: 'info' // possible values: 'info', 'success', 'error'
    },
    textMessages: {
      type: Object,
      default: { title: '', subtitle: '', content: '',  note: '' }
    }
  },
  setup(props) {
    icon = computed(function(){
      let picture, color
      switch (props.type) {
        case 'success':
          picture = 'check_circle'
          color = 'positive'
          break
        case 'error':
          picture = 'cancel'
          color = 'negative'
          break
        default:
          picture = 'info'
          color = 'info'
      }
      return { picture, color }
    })
  },
})
</script>
