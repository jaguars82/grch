<template>
  <div class="full-width" :class="{ 'topoffset-xs': $q.screen.xs, 'topoffset': $q.screen.gt.xs }"></div>
  <div>
    <q-layout
      view="hhh LpR lfr"
    >
    
    <!-- Top menu -->
    <q-header>
      <MainMenu :showUserMenuOnSmallScreen="drawers.right.is ? false : true" />
    </q-header>

    <!-- Left drawer -->
    <q-drawer
      v-if="drawers.left.is"
      v-model="drawerLeft"
      :bordered="xsOptions"
      side="left"
      :width="$q.screen.gt.md ? 400 : 300"
      :breakpoint="100"
      :mini-to-overlay="xsOptions"
      :mini="miniStateLeft"
    >
      <template v-slot:mini>
        <div :class="{ 'topoffset-xs': $q.screen.xs, 'topoffset': $q.screen.gt.xs }"></div>
        <div class="row justify-center">
          <q-btn class="q-mt-sm" round unelevated icon="menu" @click="miniStateLeft = false"/>
        </div>
      </template>

      <div :class="{ 'topoffset-xs': $q.screen.xs, 'topoffset': $q.screen.gt.xs }"></div>
      <div class="row justify-end items-center">
        <q-btn size="sm" dense class="q-my-xs q-mr-sm" round unelevated icon="close" @click="miniStateLeft = true"/>
      </div>
      <slot name="left-drawer"></slot>
    </q-drawer>

    <!-- Right drawer -->
    <q-drawer
      v-if="drawers.right.is"
      v-model="drawerRight"
      :bordered="xsOptions"
      side="right"
      :width="$q.screen.gt.md ? 400 : 300"
      :breakpoint="100"
      :mini-to-overlay="xsOptions"
      :mini="miniStateRight"
    >
      <template v-slot:mini>
        <div class="bg-primary" :class="{ 'topoffset-xs': $q.screen.xs, 'topoffset': $q.screen.gt.xs }">
          <UserMenu v-if="$q.screen.xs" />
        </div>
        <div class="row justify-center">
          <q-btn class="q-mt-sm" round unelevated icon="menu_open" @click="miniStateRight = false"/>
        </div>
      </template>

      <div class="bg-primary" :class="{ 'topoffset-xs': $q.screen.xs, 'topoffset': $q.screen.gt.xs }">
        <div class="flex justify-end">
          <UserMenu v-if="$q.screen.xs" />
        </div>
      </div>
      <div class="row justify-start items-center">
        <q-btn size="sm" dense class="q-my-xs q-ml-sm" round unelevated icon="close" @click="miniStateRight = true"/>
      </div>
      <slot name="right-drawer"></slot>
    </q-drawer>

    <!-- Page -->
    <q-page-container>
      <q-page
        :class="{ 'q-px-xl': gutters && ($q.screen.lg || $q.screen.xl) }"
      >
        <slot name="breadcrumbs"></slot>
        <div class="flex row">
          <div class="col">
            <slot name="main"></slot>
          </div>
          <div v-if="secondaryColumns" :class="`gt-xs col-${secondaryColumns}`">
            <slot name="secondary"></slot>
          </div>
        </div>
        <div class="lt-md">
          <slot name="secondary"></slot>
        </div>
        <div class="topoffset"></div>
        <ScrollToTopButton />
      </q-page>
    </q-page-container>
    
    <!-- Footer -->
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
import UserMenu from '@/Components/Layout/UserMenu.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ScrollToTopButton from '@/Components/Elements/ScrollToTopButton.vue'

export default ({
  name: 'MainLayout',
  props: {
    gutters: {
      type: Boolean,
      default: true
    },
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
    UserMenu,
    Footer,
    ScrollToTopButton
  },
  setup(props) {
    const $q = useQuasar()
    const drawerRight = ref(props.drawers.right.opened)
    const drawerLeft = ref(props.drawers.left.opened)
    const miniStateRight = ref($q.screen.lt.md ? true : false)
    const miniStateLeft = ref($q.screen.lt.md ? true : false)
    const xsOptions = ref($q.screen.lt.sm ? true : false)
    const onResize = () => {
      miniStateRight.value = $q.screen.lt.md ? true : false
      miniStateLeft.value = $q.screen.lt.md ? true : false
      xsOptions.value = $q.screen.lt.sm ? true : false
    }
    onMounted(() => {
      window.addEventListener('resize', onResize)
    })
    onUnmounted(() => {
      window.removeEventListener('resize', onResize)
    })
    return { drawerLeft, drawerRight, miniStateLeft, miniStateRight, xsOptions }
  },
})
</script>

<style scoped>
.topoffset {
  height: 58px;
  min-height: 58px;
  max-height: 58px;
}
.topoffset-xs {
  height: 50px;
  min-height: 50px;
  max-height: 50px;
}
</style>
