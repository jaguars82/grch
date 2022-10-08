<template>
  <div class="text-center">
    <q-circular-progress
      indeterminate
      :size="size"
      :color="color"
      :class="cssClass"
    />
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
    }
  }
}
</script>
