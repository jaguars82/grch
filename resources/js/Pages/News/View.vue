<template>
  <MainLayout>

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <q-card class="q-ma-md shadow-7">
        <q-card-section horizontal>
          <q-img v-if="news.image" class="col-5" :src="`/uploads/${news.image}`" />
          <q-card-section>
            <TitleSubtitle :title="news.title" :subtitle="asDateTime(news.created_at)" />
            <q-chip v-if="news.category === 1" color="orange" class="text-white">Акция</q-chip>
            <q-chip v-else-if="news.category === 2" color="primary" class="text-white">Новость</q-chip>
            <div class="q-mt-lg" v-html="news.detail"></div>
            <div v-if="news.search_link" class="q-mt-md text-right">
              <q-btn flat color="primary" label="Показать квартиры" icon="apartment" @click="showFlats" />
            </div>
          </q-card-section>
        </q-card-section>
      </q-card>
    </template>

  </MainLayout>
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { asDateTime } from '@/helpers/formatter'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import TitleSubtitle from '@/Components/Elements/TitleSubtitle.vue'
import Loading from "@/Components/Elements/Loading.vue"

export default {
  components: {
    MainLayout, Breadcrumbs, TitleSubtitle, Loading
  },
  props: {
    news: Object
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
      {
        id: 3,
        label: props.news.title,
        icon: 'newspaper',
        url: '/news',
        data: false,
        options: false
      },
    ])

    const showFlats = () => {
      Inertia.get(props.news.search_link)
    }

    return { asDateTime, breadcrumbs, showFlats }
  }
}
</script>