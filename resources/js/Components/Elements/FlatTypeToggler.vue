<template>
  <q-btn-group unelevated>
    <q-btn v-for="(button, index) in flatTypeButtons"
      :key="index"
      :color="button.color"
      :text-color="button.textColor"
      :label="button.label"
      @click="setFlatTypeButtonsActive(button, index)"
    ></q-btn>
  </q-btn-group>
</template>

<script>
import { ref, computed, watch } from 'vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    flatType: {
      type: String,
      default: '0'
    }
  },
  setup (props) {
    const model = ref([props.flatType])

    const emitter = useEmitter()
    watch (model, () => emitter.emit('flat-type-changed', model.value.join('')))

    const flatTypeButtons = computed(() => {
      return [
        {
          label: 'Стандарт',
          val: '0',
          color: model.value.indexOf('0') >= 0 ? 'primary' : 'white',
          textColor: model.value.indexOf('0') >= 0 ? 'white' : 'grey',
          onOff: model.value.indexOf('0') >= 0 ? true : false
        },
        {
          label: 'Евро',
          val: '1',
          color: model.value.indexOf('1') >= 0 ? 'primary' : 'white',
          textColor: model.value.indexOf('1') >= 0 ? 'white' : 'grey',
          onOff: model.value.indexOf('1') >= 0 ? true : false
        },
        {
          label: 'Студия',
          val: '2',
          color: model.value.indexOf('2') >= 0 ? 'primary' : 'white',
          textColor: model.value.indexOf('2') >= 0 ? 'white' : 'grey',
          onOff: model.value.indexOf('2') >= 0 ? true : false
        },
      ]
    })

    const setFlatTypeButtonsActive = (button) => {
      if(button.onOff) {
        model.value = []
      } else {
        model.value = [button.val]
      }
    }

    return { flatTypeButtons, setFlatTypeButtonsActive }
  }
}
</script>