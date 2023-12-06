<template>
  <div>
    <q-layout
      view="lhh LpR lff"
    >
    
    <q-header>
      <MainMenu />
    </q-header>

    <q-page-container>
        <q-page>
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
          <ScrollToTopButton />
      </q-page>
    </q-page-container>
    
    <q-footer>
      <Footer />
    </q-footer>
    
    </q-layout>
  </div>
</template>

<script>
import MainMenu from '@/Components/Layout/MainMenu.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ScrollToTopButton from '@/Components/Elements/ScrollToTopButton.vue'

export default ({
  name: 'MainLayout',
  props: {
    secondaryColumns: {
      type: [Number, Boolean],
      default: false,
    }
  },
  components: {
    MainMenu,
    Footer,
    ScrollToTopButton
  },
  setup() {
    
  },
})
</script>
