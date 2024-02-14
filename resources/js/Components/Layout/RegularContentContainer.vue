<template>
  <div 
    class="q-ml-md shadow-3 bg-white rounded-borders"
    :class="{ 'q-px-sm': $q.screen.lt.md, 'q-py-md': $q.screen.lt.md, 'q-pa-md': $q.screen.gt.sm }"
  >
    <TitleSubtitle v-if="title || subtitle" :title="title" :subtitle="subtitle" />
    <slot name="content"></slot>
  </div>
  
</template>

<script>
import TitleSubtitle from '@/Components/Elements/TitleSubtitle.vue'

export default {
  components: {
    TitleSubtitle
  },
  props: {
    title: String,
    subtitle: String
  }
}
</script>