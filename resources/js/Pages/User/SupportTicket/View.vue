<template>
  <div>Ticket {{ ticket.ticket_number }}</div>
</template>

<script>
import { ref, computed } from 'vue'
import ProfileLayout from '../../../Layouts/ProfileLayout.vue'
import Breadcrumbs from '../../../Components/Layout/Breadcrumbs.vue'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs
  },
  props: {
    ticket: Array
  },
  setup(props) {
    const breadcrumbs = [
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
        label: 'Техподдержка',
        icon: 'support_agent',
        url: '/user/support/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `Обращение ${props.ticket.ticket_number}`,
        icon: 'forum',
        url: '/user/support-ticket/view',
        data: { id: props.ticket.id },
        options: false
      },
    ]

    return { breadcrumbs }
  },
})
</script>