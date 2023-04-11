<template>
  <div
    v-if="paramName && paramValue"
    class="row no-wrap justify-between"
  >
    <div class="col-5 q-py-sm">
      <span>{{ paramName }}:</span>
    </div>
    <div class="col-7 text-right q-py-sm text-bold">
      <span>{{ paramValue }}</span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    paramName: {
      type: String,
      default: ''
    },
    paramValue: {
      type: String,
      default: ''
    }
  }
}
</script>