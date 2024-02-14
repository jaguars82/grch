<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>

      <div class="row q-px-md q-col-gutter-x-xs q-col-gutter-y-md">
        <template v-for="agency of dataProvider">
          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <OrganizationInfoCard
              :id="agency.id"
              :name="agency.name"
              :address="agency.address"
              :logo="agency.logo"
              icon="source_environment"
              :url="agency.url"
              :phone="agency.phone"
              pathToView="/agency/view"
            />
          </div>
        </template>
      </div>

      <div class="q-pa-lg flex flex-center">
        <q-pagination
          v-model="currentPage"
          :max="pagination.totalPages"
          :max-pages="$q.screen.xs ? 4 : 8"
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
        label: 'Агентства',
        icon: 'real_estate_agent',
        url: '/agency',
        data: false,
        options: false
      },
    ])

    const currentPage = ref(props.pagination.page + 1)

    const goToPage = function (page) {
      Inertia.get('/agency', { page: page })
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