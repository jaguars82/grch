<template>
  <div class="text-center">
    <q-circular-progress
      indeterminate
      :size="size"
      :color="color"
      :class="cssClass"
    />
  </div>
  <div v-if="text">
    <p class="text-center">{{ text }}</p>
  </div>
</template>

<script>
export default {
  props: {
    size: {
      type: String,
      default: '100px'
    },
    color: {
      type: String,
      default: 'primary'
    },
    cssClass: {
      type: String,
      default: 'q-my-lg'
    },
    text: {
      type: String
    }
  }
}
</script>
