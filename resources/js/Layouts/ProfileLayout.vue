<template>
  <div>
    <MainMenu></MainMenu>
    <slot name="breadcrumbs"></slot>
    <div class="row">
      <div class="col-3">
        <ProfileMenu></ProfileMenu>
      </div>
      <div class="col-9">
        <slot name="main"></slot>
      </div>
    </div>
    <Footer></Footer>
  </div>
</template>

<script>
import MainMenu from '../Components/Layout/MainMenu.vue'
import ProfileMenu from '../Components/Layout/ProfileMenu.vue'
import Footer from '../Components/Layout/Footer.vue'

export default ({
  name: 'MainLayout',
  components: {
    MainMenu,
    ProfileMenu,
    Footer
  },
  setup() {
    
  },
})
</script>
