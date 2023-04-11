<template>
    <MainLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <h3 class="text-center">Вторичная продажа</h3>
        <div v-for="advertisement of advertisements" :key="advertisement.id">
          <SecondaryRoomListItem
            v-for="room of advertisement.secondary_room"
            :key="room.id"
            :room="room"
            :created="advertisement.creation_date"
            :author="{
              info: advertisement.author_info
            }"
            :advId="advertisement.id"
          />          
        </div>

        <div class="q-pa-lg flex flex-center">
          <q-pagination
            v-model="currentPage"
            :max="pagination.totalPages"
            :max-pages="8"
            @update:model-value="goToPage(currentPage)"
          />
        </div>
      </template>
    </MainLayout>
  </template>
  
<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import SecondaryRoomListItem from "@/Components/SecondaryRoom/SecondaryRoomListItem.vue"
  
export default {
  props: {
    advertisements: {
      type: Array,
      default: []
    },
    pagination: {
      type: Object,
      default: {
        currPage: 1
      }
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading, SecondaryRoomListItem
  },
  setup(props) {

    const currentPage = ref(props.pagination.currPage)

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
    ])

    const goToPage = function (page) {
      Inertia.get('/secondary', { page: page })
    }

    return { breadcrumbs, currentPage, goToPage }
  }
}
</script>