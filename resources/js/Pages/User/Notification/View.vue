<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="notification.topic" :subtitle="`от ${createDate}`">
        <template v-slot:content>
          <p>
            {{ notification.body }}
          </p>
          <div v-if="notification.action_url" class="q-mt-sm text-right">
            <inertia-link :href="notification.action_url">
              <q-btn color="primary" unelevated :label="notification.action_text ? notification.action_text : 'Подробнее'" />
            </inertia-link>
          </div>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import { asDateTime } from '@/helpers/formatter' 

export default ({
components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
  },
  props: {
    notification: Array,
  },
  setup(props) {
    const createDate = computed( () => asDateTime(props.notification.created_at))

    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: false
      },
      {
        id: 2,
        label: 'Кабинет пользователя',
        icon: 'business_center',
        url: '/user/profile',
        data: false,
        options: false
      },
      {
        id: 3,
        label: 'Уведомления',
        icon: 'speaker_notes',
        url: '/user/notification/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `Уведомление ${props.notification.id}`,
        icon: 'chat_bubble',
        url: '/user/notification/view',
        data: { id: props.notification.id },
        options: false
      },
    ])
    
    return { breadcrumbs, createDate }
  },
})
</script>