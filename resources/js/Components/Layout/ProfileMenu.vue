<template>
  <q-card>
    <q-card-section>
      <q-img class="rounded-borders" :src="user.photo ? `/uploads/${user.photo}` : '/img/user-nofoto.jpg'" />
      <div class="text-center text-h4 q-mt-sm">
        {{ user.first_name }} {{ user.last_name }}
      </div>
      <div class="text-center">
        <span class="text-grey-7">{{ user.roleLabel }}</span>
      </div>
    </q-card-section>

    <q-card-section>
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

        <inertia-link v-if="user.role === 'admin'" href="/user/application/index">
          <q-item clickable v-ripple>
            <q-item-section avatar>
              <q-icon color="primary" name="real_estate_agent" />
            </q-item-section>

            <q-item-section>Заявки</q-item-section>
          </q-item>
        </inertia-link>

        <inertia-link href="/user/support/index">
        <q-item clickable v-ripple>
          <q-item-section avatar>
            <q-icon color="primary" name="support_agent" />
          </q-item-section>

          <q-item-section>Техподдержка</q-item-section>
        </q-item>
        </inertia-link>
      </q-list>
    </q-card-section>
  </q-card>
</template>

<script>
import { ref } from 'vue'
import { userInfo } from '../../helpers/shared-data'

export default ({
  setup() {
    const { user } = userInfo()
    return { user }
  },
})
</script>
