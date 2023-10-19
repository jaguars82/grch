<template>
  <div>
    <div class="q-pa-md">
      <q-toolbar class="bg-primary text-white">
        <q-avatar class="q-mr-sm">
          <a href="/">
            <img src="/img/icons/logo.svg">
          </a>
        </q-avatar>
        <template v-for="item of menuItems">
          <a v-if="item.isLink" stretch :href="item.path">
            <q-btn flat :label="item.name" />
          </a>
          <q-btn v-else stretch flat :label="item.name" @click="goPath(item.path)" />
        </template>
        
      </q-toolbar>
    </div>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'

export default ({
  name: 'MainMenu',
  setup() {
    const menuItems = [
      {
        ind: 1,
        name: 'Новости',
        path: '/news',
        isLink: true
      },
      {
        ind: 2,
        name: 'Застройщики',
        path: '/developer',
        isLink: true
      },
      {
        ind: 3,
        name: 'ЖК',
        path: '/newbuilding-complex',
        isLink: true
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
        isLink: true
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