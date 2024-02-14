<template>
  <template v-if="title">
    <inertia-link v-if="link.length" :href="link">
      <p class="q-mb-xs" :class="{ 'text-h4': $q.screen.xs, 'text-h3': $q.screen.gt.xs }">{{ title }}</p>
    </inertia-link>
    <p v-else class="q-mb-xs text-grey-7" :class="{ 'text-h4': $q.screen.xs, 'text-h3': $q.screen.gt.xs }">{{ title }}</p>
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
