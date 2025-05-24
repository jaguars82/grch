<template>
  <div class="message">
    {{ message }}
  </div>
</template>

<script>
export default {
  props: {
    message: {
      type: String,
      default: '',
    }
  }
}
</script>

<style scoped>
.message {
  padding: 5px;
  border-bottom: 1px solid #eee;
}
</style>