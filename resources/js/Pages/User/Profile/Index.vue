<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <q-card class="q-ml-md">
        <q-card-section>
          <p class="text-h3">{{ user.last_name }} {{ user.first_name }} {{ user.middle_name }}</p>
          <p class="text-h5 text-grey-7">{{ user.roleLabel }}</p>
        </q-card-section>
        <q-card-section>
          <p class="text-h6">Контакты</p>
          <div>
            <a v-if="user.phone" :href="`tel:${user.phone}`">
              <div>
              <q-icon class="q-mr-md" name="phone" />
              {{ user.phone }}
              </div>
            </a>              
            <a v-if="user.email" :href="`mailto:${user.email}`">
              <div>
              <q-icon class="q-mr-md" name="email" />
              {{ user.email }}
              </div>
            </a>              
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <a :href="`/user/profile/update?id=${user.id}`">
          <q-btn padding="xs md" unelevated rounded color="primary" icon="edit" label="Редактировать"></q-btn>
          </a>
        </q-card-actions>
      </q-card>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref } from 'vue'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs
  },
  setup() {
    const { user } = userInfo()

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
      /*{
        id: 3,
        label: 'Профиль',
        icon: 'account_box',
        url: '/user/profile/index',
        data: false,
        options: false
      },*/
    ])

    return { user, breadcrumbs }
  },
})
</script>
