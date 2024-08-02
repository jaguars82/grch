<template>
  <q-btn square :class="{ 'q-px-xs': $q.screen.xs, 'q-px-sm': $q.screen.sm }" unelevated no-caps color="primary">
    <template v-slot:default>
      <!-- Button label -->
      <div class="row items-center no-wrap">
        <q-avatar :size="$q.screen.xs ? '40px' : '50px'">
          <img :src="user.photo ? `/uploads/${user.photo}` : '/img/user-nofoto.jpg'">
          <q-badge v-if="messages.all.status" floating rounded color="orange" :label="messages.all.amount" />
        </q-avatar>
        <div class="q-mx-md text-left hidden-xs">
          <span><strong>{{ user.first_name }} {{ user.last_name }}</strong></span>
          <br />
          <span>{{ user.roleLabel }}</span>
        </div>
      </div>
      <!-- Menu items -->
      <q-popup-proxy :breakpoint="5">
        <q-list>
          <template v-for="item of items">
            <template v-if="item.options === 'native'">
              <a class="text-grey-7" v-if="!item.roles || item.roles.includes(user.role)" :href="item.url">
                <q-item clickable v-close-popup>
                  <q-item-section avatar>
                    <q-icon :name="item.icon" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ item.label }}</q-item-label>
                  </q-item-section>
                </q-item>
              </a>
            </template>
            <template v-else>
              <q-item class="text-grey-7" v-if="!item.roles || item.roles.includes(user.role)" clickable v-close-popup @click="onItemClick(item.url, item.data, item.options)">
                <q-item-section avatar>
                  <q-icon :name="item.icon" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ item.label }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </template>
        </q-list>
      </q-popup-proxy>
    </template>
  </q-btn>
</template>

<script>
import { userInfo, messagesAmount } from '@/composables/shared-data'
import { Inertia } from '@inertiajs/inertia'

export default {
  setup() {
    const { user } = userInfo()
    const { messages } = messagesAmount()

    const items = [
      {
        id: 1,
        label: 'Личный кабинет',
        icon: 'person',
        url: '/user/profile/index',
        data: false,
        options: false
      },
      {
        id: 2,
        label: 'Обучение',
        icon: 'school',
        url: '/tutorial',
        data: false,
        options: false,
        roles: ['admin', 'agent', 'manager']
      },
      {
        id: 3,
        label: 'Админ-панель',
        icon: 'settings',
        url: '/admin/index',
        data: false,
        options: 'native',
        roles: ['admin']
      },
      {
        id: 4,
        label: 'Избранное',
        icon: 'bookmark_border',
        url: '/favorite',
        data: false,
        options: false
      },
      {
        id: 5,
        label: 'Выход',
        icon: 'logout',
        url: '/auth/logout',
        data: false,
        options: false
      },
    ]

    const onItemClick = (link, data, options) => options === 'native' ? () => { window.location.replace(link) } : Inertia.get(link, data, options)
     
    return { user, messages, items, onItemClick }
  }
}
</script>