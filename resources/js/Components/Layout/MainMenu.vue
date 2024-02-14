<template>
  <div class="fixed-top">
    <q-toolbar class="bg-primary text-white q-pr-none">
      <q-avatar class="q-mr-sm">
        <inertia-link href="/">
          <img src="/img/icons/logo.svg">
        </inertia-link>
      </q-avatar>
      <template v-if="$q.screen.gt.sm">
        <template v-for="item of menuItems">
          <a v-if="item.isLink" stretch :href="item.path">
            <q-btn flat :label="item.name" />
          </a>
          <q-btn v-else stretch flat :label="item.name" @click="goPath(item.path)" />
        </template>
      </template>
      <q-btn v-else color="ptimary" unelevated round icon="menu">
        <q-menu auto-close>
          <q-list style="min-width: 150px">
            <q-item v-for="item of menuItems" clickable @click="goPath(item.path)">
              <q-item-section class="text-uppercase text-bold text-grey-7">{{ item.name }}</q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
      <q-space />
      <UserMenu v-if="!$q.screen.xs || ($q.screen.xs && showUserMenuOnSmallScreen)" />
    </q-toolbar>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import UserMenu from '@/Components/Layout/UserMenu.vue'

export default ({
  name: 'MainMenu',
  components: { UserMenu },
  props: {
    showUserMenuOnSmallScreen: {
      type: Boolean,
      default: false
    }
  },
  setup() {
    const menuItems = [
      {
        ind: 1,
        name: 'Новости',
        path: '/news',
        isLink: false
      },
      {
        ind: 2,
        name: 'Застройщики',
        path: '/developer',
        isLink: false
      },
      {
        ind: 3,
        name: 'ЖК',
        path: '/newbuilding-complex',
        isLink: false
      },
      {
        ind: 4,
        name: 'Вторичка',
        path: '/secondary',
        isLink: false
      },
      {
        ind: 5,
        name: 'Контакты',
        path: '/agency',
        isLink: false
      },
      {
        ind: 6,
        name: 'Тарифы',
        path: '/tariff',
        isLink: false
      },
    ]

    const goPath = (path) => {
      Inertia.get(path)
    }

    return {
      menuItems,
      goPath
    }
  }
})
</script>