<template>
  <div>
    <q-layout
      view="lhh LpR lff"
    >
    
    <q-header>
      <MainMenu />
    </q-header>

    <q-drawer
      v-if="drawers.left.is"
      v-model="drawerLeft"
      side="left"
      :width="300"
      :breakpoint="500"
    >
      <slot name="left-drawer"></slot>
    </q-drawer>

    <q-drawer
      v-if="drawers.right.is"
      v-model="drawerRight"
      side="right"
      :width="300"
      :breakpoint="500"
    >
      <slot name="right-drawer"></slot>
    </q-drawer>

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
import { ref } from 'vue'
import MainMenu from '@/Components/Layout/MainMenu.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ScrollToTopButton from '@/Components/Elements/ScrollToTopButton.vue'

export default ({
  name: 'MainLayout',
  props: {
    drawers: {
      type: Object,
      default: {
        left: {
          is: false,
          opened: false
        },
        right: {
          is: false,
          opened: false
        }
      }
    },
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
  setup(props) {
    const drawerRight = ref(props.drawers.right.opened)
    const drawerLeft = ref(props.drawers.left.opened)
    return { drawerLeft, drawerRight }
  },
})
</script>
