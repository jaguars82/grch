<template>
  <div 
    class="q-ml-md q-pa-md shadow-3 bg-white rounded-borders"
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
