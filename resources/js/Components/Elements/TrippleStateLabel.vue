<template>
  <div class="q-px-sm">
    <span v-if="condition === true">{{ labelTrue }}</span>
    <span v-else-if="condition === false">{{ labelFalse }}</span>
    <span v-else>{{ labelNull }}</span>
  </div>
</template>

<script>
  export default {
    props: {
      condition: {
        type: Boolean || null,
        default: null
      },
      labelTrue: {
        type: String,
        default: 'Да'
      },
      labelFalse: {
        type: String,
        default: 'Нет'
      },
      labelNull: {
        type: String,
        default: 'Не указано'
      },
    }
  }
</script>