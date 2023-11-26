<template>
  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
      viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" @click="rotateItems">
    <circle class="st0" cx="256" cy="256" r="229.5"/>
    <path class="st1" d="M256,26.5C129.2,26.5,26.5,129.2,26.5,256S129.2,485.5,256,485.5S485.5,382.8,485.5,256S382.8,26.5,256,26.5z
      M256,441.4c-102.4,0-185.4-83-185.4-185.4S153.6,70.6,256,70.6s185.4,83,185.4,185.4S358.4,441.4,256,441.4z"/>
    <path class="st2" d="M261,362l23.9-106.2l-25.1-105.9c-1.1-4.6-7.6-4.6-8.7,0.1l-23.9,106.2l25.1,105.9
      C253.4,366.7,259.9,366.6,261,362z"/>
    <path class="st2" d="M362,251.4l-106.1-24.2L150,252c-4.6,1.1-4.6,7.6,0,8.7l106.1,24.2L362,260C366.6,259,366.6,252.4,362,251.4z"
      />
    <path class="st3" d="M256,0C114.6,0,0,114.6,0,256s114.6,256,256,256s256-114.6,256-256S397.4,0,256,0z M256,476.7
      c-121.9,0-220.7-98.8-220.7-220.7S134.1,35.3,256,35.3S476.7,134.1,476.7,256S377.9,476.7,256,476.7z"/>
    <path class="st4" d="M256,35.3C134.1,35.3,35.3,134.1,35.3,256S134.1,476.7,256,476.7S476.7,377.9,476.7,256S377.9,35.3,256,35.3z
      M256,459c-112.1,0-203-90.9-203-203c0-112.1,90.9-203,203-203s203,90.9,203,203C459,368.1,368.1,459,256,459z"/>
    <path class="st2" d="M423.7,247.2h-9.3C410,166.6,345.4,102,264.8,97.5v-9.3c0-4.9-4-8.8-8.8-8.8s-8.8,4-8.8,8.8v9.3
      C166.6,102,102,166.6,97.6,247.2h-9.3c-4.9,0-8.8,4-8.8,8.8c0,4.9,4,8.8,8.8,8.8h9.3c4.5,80.6,69.1,145.2,149.6,149.6v9.3
      c0,4.9,4,8.8,8.8,8.8s8.8-4,8.8-8.8v-9.3c80.6-4.5,145.2-69.1,149.6-149.6h9.3c4.9,0,8.8-4,8.8-8.8
      C432.6,251.1,428.6,247.2,423.7,247.2z M273.5,396c-0.6-9.2-8.2-16.4-17.5-16.4c-9.3,0-16.9,7.3-17.5,16.4
      c-63.9-8-114.5-58.6-122.5-122.5c9.2-0.6,16.4-8.2,16.4-17.5c0-9.3-7.3-16.9-16.4-17.5c8-63.9,58.6-114.5,122.5-122.5
      c0.6,9.2,8.2,16.4,17.5,16.4c9.3,0,16.9-7.3,17.5-16.4c63.9,8,114.5,58.6,122.5,122.5c-9.2,0.6-16.4,8.2-16.4,17.5
      c0,9.3,7.3,16.9,16.4,17.5C388.1,337.4,337.4,388.1,273.5,396z"/>
    <g ref="compassArrow" class="compass-arrow">
      <path class="st5" d="M251.3,99.9l-35.6,156.2l36.5,156c1.1,4.6,7.6,4.5,8.6,0l35.6-156.2l-36.5-156
        C258.8,95.3,252.3,95.3,251.3,99.9z"/>
      <path class="st6" d="M215.7,256.1l80.7-0.2l-36.5-156c-1.1-4.6-7.6-4.5-8.6,0L215.7,256.1z"/>
    </g>
    <circle class="st7" cx="256" cy="256" r="17.7"/>
</svg>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    azimuth: {
      type: Number,
      default: 0
    },
  },
  setup (props) {
    const emitter = useEmitter()
    const pointsToNorth = ref(false)
    const rotationAngle = computed(() => {
      return props.azimuth < 180 ? props.azimuth : -(360 - props.azimuth)
    })

    const compassArrow = ref()

    const rotateItems = (e) => {
      if (pointsToNorth.value === true) {
        /** rotate compass arrow to azimuth */
        compassArrow.value.style.transform = 'rotate('+ rotationAngle.value +'deg)'
      } else {
        /** rotate compass arrow back to the North */
        compassArrow.value.style.transform = 'rotate(0deg)'
      }
      emitter.emit('compass-orient', { orientedToNorth: pointsToNorth.value, angle: rotationAngle.value })
      pointsToNorth.value = !pointsToNorth.value
    }

    const resetCompass = () => {
      compassArrow.value.style.transform = 'rotate('+ rotationAngle.value +'deg)'
      pointsToNorth.value = false
    }

    onMounted(() => {
      resetCompass()
    })

    return { compassArrow, resetCompass, rotateItems }
  }
}
</script>

<style scoped type="text/css">
  .st0{fill:#00D2FF;}
  .st1{fill:#28AFF0;}
  .st2{fill:#8CE6FF;}
  .st3{fill:#959CB3;}
  .st4{fill:#707487;}
  .st5{fill:#E4EAF6;}
  .st6{fill:#FF6464;}
  .st7{fill:#5B5D6E;}
  .compass-arrow {
    transform-origin: center;
    transition: transform 1s;
  }

</style>
