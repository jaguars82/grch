<template>
  <div class="q-pa-md q-gutter-sm">
    <q-breadcrumbs>
      <q-breadcrumbs-el
        :class="i != Object.keys(links).length - 1 ? 'cursor-pointer' : 'non-selectable text-grey-7'"
        v-for="(link, i) of links"
        :key="link.id"
        :icon="link.icon"
        :label="link.label"
        v-on:click="i != Object.keys(links).length - 1 ? goLink(link.url, link.data, link.options) : ''" 
      />
    </q-breadcrumbs>
  </div>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default ({
  props: {
    links: Array
  },
  setup() {

    const goLink = (link, data, options) => Inertia.get(link, data, options)

    return { goLink }
  },
})
</script>
