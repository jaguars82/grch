<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
    <p>Заявки</p>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref } from 'vue'
import ProfileLayout from '../../../Layouts/ProfileLayout.vue'
import Breadcrumbs from '../../../Components/Layout/Breadcrumbs.vue'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs
  },
  props: {
    tickets: Array
  },
  setup() {
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
        label: 'Заявки',
        icon: 'real_estate_agent',
        url: '/user/application/index',
        data: false,
        options: false
      },
    ])

    return { breadcrumbs }
  },
})
</script>
