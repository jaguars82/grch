<template>
  <div>
    <q-toolbar class="bg-primary text-white">
      <q-avatar class="q-mr-sm">
        <inertia-link href="/">
          <img src="/img/icons/logo.svg">
        </inertia-link>
      </q-avatar>
      <template v-for="item of menuItems">
        <a v-if="item.isLink" stretch :href="item.path">
          <q-btn flat :label="item.name" />
        </a>
        <q-btn v-else stretch flat :label="item.name" @click="goPath(item.path)" />
      </template>
      <q-space />
      <UserMenu />
    </q-toolbar>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import UserMenu from '@/Components/Layout/UserMenu.vue'

export default ({
  name: 'MainMenu',
  components: { UserMenu },
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