<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <h3 class="text-center">Новости и акции</h3>
      <NewsItem class="q-mx-md" v-for="post of posts" :item="post" />

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
import NewsItem from '@/Components/News/NewsItem.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"

export default {
  props: {
    posts: {
      type: Array,
      derfault: []
    },
    pagination: {
      type: Object,
    }
  },
  components: {
    MainLayout, NewsItem, Breadcrumbs, Loading
  },
  setup(props) {
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
        label: 'Новости и акции',
        icon: 'feed',
        url: '/news',
        data: false,
        options: false
      },
    ])

    /*const getCurrentPage = computed(() => {
      props.pagination.page + 1
    })*/

    const currentPage = ref(props.pagination.page + 1)

    const goToPage = function (page) {
      Inertia.get('/news', { page: page })
    }

    return {
      breadcrumbs,
      currentPage,
      goToPage
    }
  },
}
</script>