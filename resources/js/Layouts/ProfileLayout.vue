<template>
  <div>
    <q-layout
      view="hhh LpR fff"
    >

    <q-header>
      <MainMenu />
    </q-header>

    <q-drawer
      v-model="drawerLeft"
      side="left"
      :width="300"
      :breakpoint="500"
    >
      <ProfileMenu></ProfileMenu>
    </q-drawer>

    <q-drawer
      v-if="rightDrawer.is"
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
        <div class="row">
          <div class="col-12 q-pr-md">
            <slot name="main"></slot>
          </div>
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
import ProfileMenu from '@/Components/Layout/ProfileMenu.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ScrollToTopButton from '@/Components/Elements/ScrollToTopButton.vue'

export default ({
  name: 'MainLayout',
  props: {
    rightDrawer: {
      type: Object,
      default: {
        is: false,
        opened: false
      },
    },
  },
  components: {
    MainMenu,
    ProfileMenu,
    Footer,
    ScrollToTopButton
  },
  setup(props) {
    const drawerRight = ref(props.rightDrawer.opened)
    const drawerLeft = ref(true)
    return { drawerLeft, drawerRight }
  },
})
</script>
