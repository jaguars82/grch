<template>
  <div>Меню профиля {{ user.first_name }}</div>

  <q-list bordered>
    <inertia-link href="/user/profile/index">
    <q-item clickable v-ripple>
      <q-item-section avatar>
        <q-icon color="primary" name="account_box" />
      </q-item-section>

      <q-item-section>Профиль</q-item-section>
    </q-item>
    </inertia-link>

    <q-item v-if="user.role === 'admin'" clickable v-ripple>
      <q-item-section avatar>
        <q-icon color="primary" name="people" />
      </q-item-section>

      <q-item-section>Агенты</q-item-section>
    </q-item>

    <inertia-link href="/user/support/index">
    <q-item clickable v-ripple>
      <q-item-section avatar>
        <q-icon color="primary" name="support_agent" />
      </q-item-section>

      <q-item-section>Техподдержка</q-item-section>
    </q-item>
    </inertia-link>
  </q-list>

</template>

<script>
import { ref } from 'vue'
import { userInfo } from '../../helpers/shared-data'

export default ({
  setup() {
    const { user } = userInfo()
    console.log(user)

    return { user }
  },
})
</script>
