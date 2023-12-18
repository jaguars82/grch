<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>

      <div class="row q-px-md q-col-gutter-x-xs q-col-gutter-y-md">
        <template v-for="developer of dataProvider">
          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <OrganizationInfoCard
              :id="developer.id"
              :name="developer.name"
              :address="developer.address"
              :logo="developer.logo"
              icon="source_environment"
              :url="developer.url"
              :phone="developer.phone"
              pathToView="/developer/view"
            />
          </div>
        </template>
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
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import OrganizationInfoCard from '@/Components/Organization/OrganizationInfoCard.vue'
import Loading from "@/Components/Elements/Loading.vue"

export default {
  props: {
    dataProvider: {
      type: Object,
      derfault: {}
    },
    pagination: {
      type: Object,
    }
  },
  components: {
    MainLayout, Breadcrumbs, OrganizationInfoCard, Loading
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
        label: 'Застройщики',
        icon: 'engineering',
        url: '/developer',
        data: false,
        options: false
      },
    ])

    const currentPage = ref(props.pagination.page + 1)

    const goToPage = function (page) {
      Inertia.get('/developer', { page: page })
    }

    return {
      breadcrumbs,
      currentPage,
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