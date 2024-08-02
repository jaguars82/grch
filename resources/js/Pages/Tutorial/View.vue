<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <q-card class="q-ma-md shadow-7">
        <q-card-section>
          <h3>{{ lesson.title }}</h3>
          <h4 v-if="lesson.subtitle">{{ lesson.subtutle }}</h4>
          <p v-if="lesson.description">{{ lesson.description }}</p>

          <!--<iframe width="560" height="315" :src="lesson.video_source" :title="lesson.title" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>-->

          <div>
            <q-video
              :src="lesson.video_source"
            />
          </div>

          <div v-if="lesson.content" v-html="lesson.content"></div>
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
  components: {
    MainLayout, Breadcrumbs, Loading
  },
  props: {
    lesson: {
      type: Object,
      default: {},
    }
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
        label: 'Обучение',
        icon: 'school',
        url: '/tutorial',
        data: false,
        options: false
      },
      {
        id: 3,
        label: props.lesson.title,
        icon: 'play_lesson',
        url: `/tutorial/view?id=${props.lesson.id}`,
        data: false,
        options: false
      },
    ])

    const onGoToLessonList = () => {
      Inertia.get('/tutorial/index')
    }

    return { breadcrumbs, onGoToLessonList }
  }
}
</script>