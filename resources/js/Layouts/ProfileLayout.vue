<template>
  <div class="topoffset"></div>
  <div>
    <q-layout
      view="hhh LpR lfr"
    >

    <q-header>
      <MainMenu />
    </q-header>

    <q-drawer
      v-model="drawerLeft"
      :bordered="xsOptions"
      side="left"
      :width="300"
      :breakpoint="100"
      :mini-to-overlay="xsOptions"
      :mini="miniState"
    >
      <template v-slot:mini>
        <div class="topoffset"></div>
        <div class="row justify-center">
          <q-btn class="q-mt-sm" round unelevated icon="menu" @click="miniState = false"/>
        </div>
      </template>

      <div class="topoffset"></div>
      <div class="row justify-end items-center">
        <q-btn size="sm" dense class="q-my-xs q-mr-sm" round unelevated icon="close" @click="miniState = true"/>
      </div>
      <ProfileMenu></ProfileMenu>
    </q-drawer>

    <q-drawer
      v-if="rightDrawer.is"
      v-model="drawerRight"
      side="right"
      :width="300"
      :breakpoint="500"
    >
      <div class="topoffset"></div>
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
        <div class="topoffset"></div>
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
import { ref, onMounted, onUnmounted } from 'vue'
import { useQuasar } from 'quasar'
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
    const $q = useQuasar()
    const drawerRight = ref(props.rightDrawer.opened)
    const drawerLeft = ref(true)
    const miniState = ref($q.screen.lt.md ? true : false)
    const xsOptions = ref($q.screen.lt.sm ? true : false)
    const onResize = () => {
      miniState.value = $q.screen.lt.md ? true : false
      xsOptions.value = $q.screen.lt.sm ? true : false
    }
    onMounted(() => {
      window.addEventListener('resize', onResize)
    })
    onUnmounted(() => {
      window.removeEventListener('resize', onResize)
    })
    return { drawerLeft, drawerRight, miniState, xsOptions }
  },
})
</script>

<style scoped>
.topoffset {
  height: 58px;
  min-height: 58px;
  max-height: 58px;
}
</style>