<template>
  <div>
    <MainMenu></MainMenu>
    <slot name="breadcrumbs"></slot>
    <div class="flex row">
      <div class="col">
        <slot name="main"></slot>
      </div>
      <div v-if="secondaryColumns" :class="`gt-sm col-${secondaryColumns}`">
        <slot name="secondary"></slot>
      </div>
    </div>
    <div class="lt-md">
      <slot name="secondary"></slot>
    </div>
    <Footer></Footer>
  </div>
</template>

<script>
import MainMenu from '../Components/Layout/MainMenu.vue'
import Footer from '../Components/Layout/Footer.vue'

export default ({
  name: 'MainLayout',
  props: {
    secondaryColumns: {
      type: Number || Boolean,
      default: false,
    }
  },
  components: {
    MainMenu,
    Footer
  },
  setup() {
    
  },
})
</script>
