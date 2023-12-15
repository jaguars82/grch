<template>
  <MainLayout>
    
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <template v-for="developer of complexes">
        <h3 class="q-mx-md q-mt-md q-mb-sm text-center">{{ developer.name }}</h3>
        <div class="row q-pl-md q-col-gutter-x-xs q-col-gutter-y-md">
          <template v-for="complex of developer.complexes">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
              <q-card
                class="full-height cursor-pointer"
                @click="goToComplex(complex.id)"
                @mouseenter="focusOn"
                @mouseleave="focusOff"
              >
                <q-card-section>
                  <q-img class="item-img" fit="scale-down" :src="complex.logo ? `/uploads/${complex.logo}` : `/img/newbuilding-complex.png`" :alt="complex.name" />
                </q-card-section>
                <q-card-section>
                  <p class="text-h4 text-center">{{ complex.name }}</p>
                </q-card-section>
              </q-card>
            </div>
          </template>
        </div>
      </template>

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

export default {
  props: {
    complexes: {
      type: Object,
      derfault: {}
    },
    pagination: {
      type: Object,
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading
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
        label: 'Жилые комплексы',
        icon: 'domain',
        url: '/newbuilding-complex',
        data: false,
        options: false
      },
    ])

    const focusOn = function (event) {
      event.target.classList.add('shadow-15')
    }

    const focusOff = function (event) {
      event.target.classList.remove('shadow-15')
    }

    const currentPage = ref(props.pagination.page + 1)

    const goToPage = function (page) {
      Inertia.get('/newbuilding-complex', { page: page })
    }

    const goToComplex = function (complexId) {
      Inertia.get('/newbuilding-complex/view', { id: complexId })
    }

    return {
      breadcrumbs,
      focusOn,
      focusOff,
      currentPage,
      goToComplex,
      goToPage
    }
  },
}
</script>

<style scoped>
  .item-img {
    height: 120px;
  }
</style>