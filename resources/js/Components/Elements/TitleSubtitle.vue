<template>
  <template v-if="title">
    <inertia-link v-if="link.length" :href="link">
      <p class="q-mb-xs text-h3">{{ title }}</p>
    </inertia-link>
    <p v-else class="q-mb-xs text-h3 text-grey-7">{{ title }}</p>
  </template>
  <p v-if="subtitle" class="text-h5">{{ subtitle }}</p>
</template>

<script>
export default {
  props: {
    title: String,
    subtitle: String,
    link: {
      type: String,
      default: ''
    }
  }
}
</script>
