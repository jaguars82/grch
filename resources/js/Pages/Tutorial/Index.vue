<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <q-card class="q-ma-md shadow-7">
        <q-card-section>
          <h3 class="text-center">Материалы для обучения</h3>
          <q-card class="q-mt-sm no-shadow" bordered v-for="(lesson, i) of lessons">
            <q-card-section horizontal>
              <q-card-section class="col-10 q-py-xs">
                <h4>{{ i + 1 }}. {{ lesson.title }}</h4>
                <h3 class="q-my-none" v-if="lesson.subtitle">{{ lesson.subtutle }}</h3>
                <p v-if="lesson.description">{{ lesson.description }}</p>
              </q-card-section>
              <q-card-section class="col-2 q-py-xs flex flex-center">
                <q-btn
                  :size="$q.screen.gt.sm ? 'md' : 'lg'"
                  :label="$q.screen.gt.sm ? 'Смотреть' : ''"
                  color="primary"
                  flat
                  :padding="$q.screen.gt.xs ? 'xs md' : 'sm'"
                  :round="$q.screen.xs"
                  :rounded="$q.screen.gt.xs"
                  @click="onOpenLesson(lesson.id)"
                  icon="play_circle"
                />
              </q-card-section>
            </q-card-section>
          </q-card>
        </q-card-section>
        <q-card-section>
        </q-card-section>
      </q-card>
    </template>
  </MainLayout>
  <pre>{{ lessons }}</pre>
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
    lessons: {
      type: Array,
      default: [],
    }
  },
  setup() {
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
    ])

    const onOpenLesson = (lessonId) => {
      Inertia.get('/tutorial/view', { id: lessonId })
    }

    return { breadcrumbs, onOpenLesson }
  }
}
</script>