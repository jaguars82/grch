<template>
  <div>
    <h1>Reserve Flat</h1>
    <h1>{{ number }}</h1>
  </div>
</template>

<script>

import { ref } from 'vue'

export default ({
  props: {
    note: String
  },
  setup(props) {
    const number = ref(99)
    return {number}
  },
})
</script>
