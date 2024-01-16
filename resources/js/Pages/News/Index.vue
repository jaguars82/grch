<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: true } }">
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

    <template v-slot:right-drawer>
      <p class="q-mb-md text-h4 text-center">Группы новостей</p>
      <q-list>
        <q-item clickable @click="goToAllNews">
          <div class="row no-wrap q-py-xs items-center full-width">
            <q-avatar rounded class="text-grey-7" color="grey-1" size="50px" icon="view_list" />
            <div class="q-pl-md ellipsis">Все новости и акции</div>
            <q-space />
            <!--<q-badge class="q-mr-md bg-white" rounded outline color="orange-3"></q-badge>-->
          </div>
        </q-item>
        <template v-for="developer of newbuildingComplexesDataProvider">
          <q-expansion-item
            v-if="developer.newsCount > 0"
            expand-icon-toggle
            expand-separator
            icon="perm_identity"
            :label="developer.name"
            header-class="bg-grey-3"
          >
            <template v-slot:header>
              <div class="row items-center full-width cursor-pointer" @click="goToNewsForDeveloper(developer.id)">
                <q-img v-if="developer.logo" class="bg-white rounded-borders" height="50px" width="50px" fit="scale-down" :src="`/uploads/${developer.logo}`" />
                <q-avatar v-else rounded class="text-grey-7" color="white" size="50px" icon="engineering" />
                <div class="q-pl-md">{{ developer.name }}</div>
                <q-space />
                <q-badge class="q-mr-md bg-white" rounded outline color="orange">{{ developer.newsCount }}</q-badge>
              </div>
            </template>
            <q-list>
              <template v-for="complex of developer.complexes">
                <q-item v-if="complex.newsCount > 0" class="cursor-pointer" dense clickable @click="goToNewsForComplex(complex.id)">
                  <div class="row no-wrap q-py-xs items-center full-width">
                    <q-img v-if="complex.logo" class="bg-grey-1 rounded-borders" height="50px" width="50px" fit="scale-down" :src="`/uploads/${complex.logo}`" />
                    <q-avatar v-else rounded class="text-grey-7" color="grey-1" size="50px" icon="engineering" />
                    <div class="q-pl-md ellipsis">{{ complex.name }}</div>
                    <q-space />
                    <q-badge class="q-mr-md bg-white" rounded outline color="orange-3">{{ complex.newsCount }}</q-badge>
                  </div>
                </q-item>
              </template>
            </q-list>
          </q-expansion-item>
        </template>
      </q-list>
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
    },
    newbuildingComplexesDataProvider: Array,
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

    const goToAllNews = function () {
      Inertia.get('/news')
    }

    const goToNewsForDeveloper = function (developerId) {
      Inertia.get('/news', { developer: developerId })
    }

    const goToNewsForComplex = function (complexId) {
      Inertia.get('/news', { 'newbuilding-complex': complexId })
    }

    return {
      breadcrumbs,
      currentPage,
      goToPage,
      goToAllNews,
      goToNewsForDeveloper,
      goToNewsForComplex
    }
  },
}
</script>