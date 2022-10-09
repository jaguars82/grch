<template>
  <q-badge v-if="amount > 0" rounded color="orange" :label="amount" />
</template>

<script>

export default {
  props: {
    amount: {
      type: Number,
      default: 0
    }
  },
  setup() {
  },
}
</script>
