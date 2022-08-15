<template>
  <div>
    <MainMenu></MainMenu>
    <slot name="main"></slot>
    <Footer></Footer>
  </div>
</template>

<script>
import MainMenu from '../Components/Layout/MainMenu.vue'
import Footer from '../Components/Layout/Footer.vue'

export default ({
  name: 'MainLayout',
  components: {
    MainMenu,
    Footer
  },
  setup() {
    
  },
})
</script>
