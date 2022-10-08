<template>
  <p v-if="title" class="q-mb-xs text-h3 text-grey-7">{{ title }}</p>
  <p v-if="subtitle" class="text-h5">{{ subtitle }}</p>
</template>

<script>
export default {
  props: {
    title: String,
    subtitle: String
  }
}
</script>
