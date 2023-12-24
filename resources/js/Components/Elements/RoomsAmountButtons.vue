<template>
  <q-btn-group unelevated>
    <q-btn v-for="(button, index) in roomButtons"
      :key="index"
      :color="button.color"
      :text-color="button.textColor"
      :label="button.label"
      @click="setRoomButtonActive(button, index)"
    ></q-btn>
  </q-btn-group>
</template>

<script>
import { ref, computed, watch } from 'vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    roomsAmount: {
      type: Array,
      default: []
    }
  },
  setup (props) {
    const currentRoomsAmountState = ref(props.roomsAmount)
    
    const emitter = useEmitter()

    watch (currentRoomsAmountState.value, () => emitter.emit('rooms-amont-changed', currentRoomsAmountState.value))

    const roomButtons = computed(() => {
      return [
        {
          label: '1',
          val: '1',
          color: currentRoomsAmountState.value.indexOf('1') >= 0 ? 'primary' : 'white',
          textColor: currentRoomsAmountState.value.indexOf('1') >= 0 ? 'white' : 'grey',
          onOff: currentRoomsAmountState.value.indexOf('1') >= 0 ? true : false
        },
        {
          label: '2',
          val: '2',
          color: currentRoomsAmountState.value.indexOf('2') >= 0 ? 'primary' : 'white',
          textColor: currentRoomsAmountState.value.indexOf('2') >= 0 ? 'white' : 'grey',
          onOff: currentRoomsAmountState.value.indexOf('2') >= 0 ? true : false
        },
        {
          label: '3',
          val: '3',
          color: currentRoomsAmountState.value.indexOf('3') >= 0 ? 'primary' : 'white',
          textColor: currentRoomsAmountState.value.indexOf('3') >= 0 ? 'white' : 'grey',
          onOff: currentRoomsAmountState.value.indexOf('3') >= 0 ? true : false
        },
        {
          label: '4',
          val: '4',
          color: currentRoomsAmountState.value.indexOf('4') >= 0 ? 'primary' : 'white',
          textColor: currentRoomsAmountState.value.indexOf('4') >= 0 ? 'white' : 'grey',
          onOff: currentRoomsAmountState.value.indexOf('4') >= 0 ? true : false
        },
        {
          label: '5',
          val: '5',
          color: currentRoomsAmountState.value.indexOf('5') >= 0 ? 'primary' : 'white',
          textColor: currentRoomsAmountState.value.indexOf('5') >= 0 ? 'white' : 'grey',
          onOff: currentRoomsAmountState.value.indexOf('5') >= 0 ? true : false
        },
        {
          label: '5+',
          val: '5+',
          color: currentRoomsAmountState.value.indexOf('5+') >= 0 ? 'primary' : 'white',
          textColor: currentRoomsAmountState.value.indexOf('5+') >= 0 ? 'white' : 'grey',
          onOff: currentRoomsAmountState.value.indexOf('5+') >= 0 ? true : false
        },
      ]
    })

    const setRoomButtonActive = (button, index) => {
      if(button.onOff) {
        const elemInd = currentRoomsAmountState.value.indexOf(button.val)
        currentRoomsAmountState.value.splice(elemInd, 1)
      } else {
        currentRoomsAmountState.value.push(button.val)
      }
    }

    const setRoomButtonsInactive = () => {
      currentRoomsAmountState.value = []
    }

    return {
      roomButtons,
      setRoomButtonActive,
      setRoomButtonsInactive
    }

  }
}
</script>