<template>
  <MainLayout :gutters="false">
    <template v-slot:main>
      <div
        class="row items-center justify-center q-pa-lg header-img"
        style="background-image: url(/img/search-index-back.jpg);"
      >
      <!--<q-parallax
        src="/img/search-index-back.jpg"
      >-->
        <!-- Search form -->
        <MainFlatFilter
          :initialFilterParams="initialFilterParams"
          :regions="regions"
          :developers="developers"
          :newbuildingComplexes="newbuildingComplexes"
          :forCurrentRegion="forCurrentRegion"
        />
        <!-- Info cards -->
        <div class="row" :class="{ 'width-98': $q.screen.lt.md, 'width-80': $q.screen.gt.sm }">
          <!-- News slider -->
          <div class="col-12 col-md-6">
            <q-card class="header-card" :class="{ 'q-mt-md': $q.screen.gt.sm, 'q-mt-none': $q.screen.lt.md }">
              <q-card-section :class="{ 'q-pa-none': $q.screen.lt.md }">
                <q-carousel
                  v-if="newsList.length"
                  v-model="newsSlide"
                  infinite
                  autoplay
                  transition-prev="jump-right"
                  transition-next="jump-left"
                  swipeable
                  animated
                  control-color="white"
                  padding
                  arrows
                  height="240px"
                  class="bg-transparent text-white no-scroll overflow-hidden"
                >
                  <q-carousel-slide
                    v-for="news of newsList"
                    :name="news.id"
                    class="column no-wrap flex-center"
                  >
                    <div class="slide-content">
                      <p class="ellipsis" :class="{ 'text-h3': $q.screen.gt.sm, 'text-h4': $q.screen.lt.md }">{{ news.title }}</p>
                      <div class="q-mt-md row" style="height: 200px;">
                        <q-img class="col-4" v-if="news.image" :src="`/uploads/${news.image}`" />
                        <div class="q-px-sm" :class="{'col-8': news.image, 'col-12': !news.image}">
                        <q-badge v-if="news.category === 1" color="orange" class="text-white">Акция</q-badge>
                        <q-badge v-else-if="news.category === 2" color="primary" class="text-white">Новость</q-badge>
                        <div class="text-h5 text-grey">{{ asDateTime(news.created_at) }}</div>
                        <div class="news-content-preview q-mt-sm ellipsis-3-lines">{{ stripHtml(news.detail) }}</div>
                        <div class="full-width q-mt-md text-right">
                          <q-btn outline rounded color="white" size="sm" icon="east" label="Открыть" @click="goToNews(news.id)" />
                        </div>
                        </div>
                      </div>
                    </div>
                  </q-carousel-slide>
                </q-carousel>
              </q-card-section>
            </q-card>
          </div>
          <!-- Flats by room -->
          <div class="col-6 col-md-3">
            <q-card class="header-card q-mt-md" :class="{ 'q-py-md': $q.screen.gt.sm, 'q-py-none': $q.screen.lt.md }">
              <q-card-section>
                <p class="ellipsis text-center text-white" :class="{ 'text-h3': $q.screen.gt.sm, 'text-h4': $q.screen.lt.md }">По количеству комнат</p>
                <div v-for="param of flatsByParams.byRoom" class="q-mt-lg row justify-between">
                  <div class="text-white">{{ param.param }}</div>
                  <div class="text-white">{{ param.val }}</div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- Flats by deadline -->
          <div class="col-6 col-md-3">
            <q-card class="header-card q-mt-md" :class="{ 'q-py-md': $q.screen.gt.sm, 'q-py-none': $q.screen.lt.md }">
              <q-card-section>
                <p class="ellipsis text-center text-white" :class="{ 'text-h3': $q.screen.gt.sm, 'text-h4': $q.screen.lt.md }">По сроку сдачи</p>
                <div v-for="param of flatsByParams.byDeadline" class="q-mt-lg row justify-between">
                  <div class="text-white">{{ param.param }}</div>
                  <div class="text-white">{{ param.val }}</div>
                </div>
              </q-card-section>
            </q-card>
          </div>
        </div>

      <!--</q-parallax>-->
      </div>

      <div class="row">
        <div class="col-12 text-center">
          <h3 class="q-my-md">Застройщики</h3>
        </div>
        <q-virtual-scroll
          class="q-mx-md hide-scrollbar"
          :items="developerDataProvider"
          virtual-scroll-horizontal
          v-slot="{ item, index }"
        >
          <div class="q-mx-sm q-mb-lg"
            :key="index"
          >
            <DeveloperResumeCard :developer="item" />
          </div>
        </q-virtual-scroll>
      </div>
    </template>
  </MainLayout>
</template>


<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { asDateTime } from '@/helpers/formatter'
import { stripHtml } from '@/helpers/utils'
import MainLayout from '@/Layouts/MainLayout.vue'
import MainFlatFilter from '@/Pages/Main/partials/MainFlatFilter.vue'
import DeveloperResumeCard from '@/Components/Developer/DeveloperResumeCard.vue'

export default {
  props: {
    searchModel: Object,
    newsList: {
      type: Array
    },
    flatsByParams: Object,
    initialFilterParams: Object,
    // newsDataProvider: Object,
    // actionsDataProvider: Object,
    developerDataProvider: Array,
    agencyDataProvider: Array,
    bankDataProvider: Array,
    regions: Object,
    // districts: Object,
    developers: Object,
    newbuildingComplexes: Object,
    forCurrentRegion: Object
  },
  components: { MainLayout, MainFlatFilter, DeveloperResumeCard },
  setup(props) {
    const newsSlide = ref(props.newsList[0].id ?? false)
    const goToNews = (newsId) => {
      Inertia.get('/news/view', { id: newsId })
    }

    return {
      asDateTime,
      stripHtml,
      newsSlide,
      goToNews,
    }
  },
}
</script>

<style scoped>
.row.justify-between::before, .row.justify-between::after {
  display: none;
}
.header-img {
  background-repeat: no-repeat;
  background-size: cover;
}
.width-80 {
  width: 80%;
}
.width-98 {
  width: 98%;
}
.header-card {
  background-color: rgba(0,0,0,.6);
}
.slide-content {
  max-width: 100%;
  max-height: 100%;
  overflow: hidden;
}
.popup-controls-container {
  width: 300px;
}
.no-padding {
  padding-right: 0;
  padding-left: 0;
  padding-top: 0;
  padding-bottom: 0;
}
</style>