<template>
    <MainLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <h3 class="text-center">Предложение #{{ advertisement.id }}</h3>
        <SecondaryRoomViewItem
        v-for="room of advertisement.secondary_room"
        :key="room.id"
        :room="room"
        :created="advertisement.creation_date"
        :author="{
            info: advertisement.author_info
        }"
        />          

      </template>
    </MainLayout>
  </template>
  
<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import SecondaryRoomViewItem from "@/Components/SecondaryRoom/SecondaryRoomViewItem.vue"
  
export default {
  props: {
    advertisement: {
      type: Object,
      default: []
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading, SecondaryRoomViewItem
  },
  setup(props) {

    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: 'native'
      },
      {
        id: 2,
        label: 'Вторичная продажа',
        icon: 'home_work',
        url: '/secondary',
        data: false,
        options: false
      },
      {
        id: 3,
        label: `Предложение #${props.advertisement.id}`,
        icon: 'newspaper',
        url: `/secondary/view?id=${props.advertisement.id}`,
        data: false,
        options: false
      },
    ])

    const goBack = function (page) {
      //Inertia.get('/secondary', { page: page })
    }

    return { breadcrumbs, goBack }
  }
}
</script>