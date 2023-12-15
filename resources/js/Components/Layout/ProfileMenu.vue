<template>
  <q-card class="no-shadow">
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

         <inertia-link
          v-if="user.role === 'manager'"
         :href="`/user/agency-agent/index?agencyId=${user.agency_id}`"
        >
          <q-item clickable v-ripple>
            <q-item-section avatar>
              <q-icon color="primary" name="people" />
            </q-item-section>
            <q-item-section>Агенты</q-item-section>
          </q-item>
         </inertia-link>

        <inertia-link
          v-if="user.role === 'admin'
              || user.role === 'manager'
              || user.role === 'agent'"
          href="/user/secondary/index"
        >
          <q-item clickable v-ripple>
            <q-item-section avatar>
              <q-icon color="primary" name="home_work" />
            </q-item-section>

            <q-item-section>Вторичка</q-item-section>
          </q-item>
        </inertia-link>

        <inertia-link
          v-if="user.role === 'admin'
              || user.role === 'manager'
              || user.role === 'agent'
              || user.role ==='developer_repres'"
          href="/user/application/index"
        >
          <q-item clickable v-ripple>
            <q-item-section avatar>
              <q-icon color="primary" name="real_estate_agent" />
            </q-item-section>

            <q-item-section>Заявки</q-item-section>
          </q-item>
        </inertia-link>

        <inertia-link
          v-if="user.role === 'admin'
              || user.role === 'manager'
              || user.role === 'agent'"
          href="/user/commercial/index"
        >
          <q-item clickable v-ripple>
            <q-item-section avatar>
              <q-icon color="primary" name="share" />
            </q-item-section>

            <q-item-section>КП</q-item-section>
          </q-item>
        </inertia-link>

        <inertia-link href="/user/support/index">
        <q-item clickable v-ripple>
          <q-item-section avatar>
            <q-icon color="primary" name="support_agent" />
          </q-item-section>

          <q-item-section>
            <div class="btn-text-with-badge">
              <span>Техподдержка</span>
              <StatusIndicator :amount="messages.support" />
            </div>
          </q-item-section>
        </q-item>
        </inertia-link>

        <inertia-link href="/user/notification/index">
        <q-item clickable v-ripple>
          <q-item-section avatar>
            <q-icon color="primary" name="speaker_notes" />
          </q-item-section>

          <q-item-section>
            <div class="btn-text-with-badge">
              <span>Уведомления</span>
              <StatusIndicator :amount="messages.notifications" />
            </div>
          </q-item-section>
        </q-item>
        </inertia-link>
      </q-list>
    </q-card-section>
  </q-card>
</template>

<script>
import { ref } from 'vue'
import { userInfo, messagesAmount } from '@/composables/shared-data'
import StatusIndicator from '@/Components/StatusIndicator.vue'

export default ({
  components: {
    StatusIndicator
  },
  setup() {
    const { user } = userInfo()
    const { messages } = messagesAmount()
    return { user, messages }
  },
})
</script>

<style scoped>
  .btn-text-with-badge {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
</style>