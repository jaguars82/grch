<template>
  <q-badge v-if="amount > 0" rounded color="orange" :label="amount" />
</template>

<script>

export default {
  props: {
    amount: {
      type: String,
      default: 0
    }
  },
  setup() {
  },
}
</script>
