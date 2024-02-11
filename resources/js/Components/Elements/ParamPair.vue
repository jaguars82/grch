<template>
  <div
    v-if="paramName && paramValue !== false"
    class="row no-wrap justify-between"
    :class="{ 'cursor-pointer': link.length, 'rounded-borders': link.length }"
    @click="goLink"
    @mouseenter="highlightLink"
    @mouseleave="unHighlightLink"
  >
    <div class="col-5 q-py-sm" :class="{ 'q-pl-sm': link.length, 'text-grey': !link.length }">
      <span :class="{ 'text-blue-9': link.length }">{{ paramName }}:</span>
    </div>
    <div class="col-7 text-right q-py-sm text-bold" :class="{ 'q-pr-sm': link.length }">
      <span v-if="!customContentProvided" :class="{ 'text-blue-9': link.length }">{{ Array.isArray(paramValue) ? paramValue.join(', ') : paramValue }}</span>
      <slot name="customValue"></slot>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    paramName: {
      type: String,
      default: ''
    },
    paramValue: {
      type: [String, Number, Array, Boolean],
      default: ''
    },
    link: {
      type: [String, Boolean],
      default: false
    }
  },
  setup (props, { slots }) {
    const goLink = () => {
      if (typeof props.link === 'string' && props.link.length > 0) {
        Inertia.get(props.link)
      }
    }

    const highlightLink = (event) => {
      if (typeof props.link === 'string' && props.link.length > 0) {
        event.target.classList.add('bg-grey-3')
      }
    }

    const unHighlightLink = (event) => {
      if (typeof props.link === 'string' && props.link.length > 0) {
        event.target.classList.remove('bg-grey-3')
      }
    }

    const customContentProvided = computed(() => {
      return 'customValue' in slots && typeof slots.customValue === 'function' ? true : false
    })

    return {
      goLink,
      highlightLink,
      unHighlightLink,
      customContentProvided
    }
  }
}
</script>