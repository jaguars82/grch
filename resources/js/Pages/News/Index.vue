<template>
    <MainLayout>
      <template v-slot:breadcrumbs>
        <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
      </template>
      <template v-slot:main>
        <q-card class="q-my-md shadow-7">
          <q-card-section>
            <h3 class="text-center">Таблица тарифов</h3>
          </q-card-section>
          <q-card-section>
          </q-card-section>
        </q-card>
      </template>
    </MainLayout>
  </template>
  
  <script>
  import { ref, computed } from 'vue'
  import { Inertia } from '@inertiajs/inertia'
  import MainLayout from '@/Layouts/MainLayout.vue'
  import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
  import Loading from "@/Components/Elements/Loading.vue"
  
  export default {
    props: {
      model: {
        type: Array,
        derfault: []
      },
      developers: {
        type: Array,
        default: []
      }
    },
    components: {
      MainLayout, Breadcrumbs, Loading
    },
    setup() {
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
          label: 'Таблица тарифов',
          icon: 'toc',
          url: '/tariff',
          data: false,
          options: false
        },
      ])
  
      return { breadcrumbs }
    },
  }
  </script>