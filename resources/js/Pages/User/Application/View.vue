<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer>
        <template v-slot:content>
          <TitleSubtitle
            :title="`Заявка ${application.application_number}`"
            :subtitle="`от ${createDate}`"
          />
          <p>
            Статус: <span class="text-lowercase">{{ statusMap[application.status] }}</span>
            <span> (последнее обновление {{ updateDate }})</span>
          </p>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import TitleSubtitle from '@/Components/Elements/TitleSubtitle.vue'
import { asDateTime } from '../../../helpers/formatter' 

export default ({
components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    TitleSubtitle
  },
  props: {
    application: Array,
    statusMap: Array
  },
  setup(props) {
    const createDate = computed( () => asDateTime(props.application.created_at))
    const updateDate = computed( () => asDateTime(props.application.update_at))

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
        label: 'Кабинет пользователя',
        icon: 'business_center',
        url: '/user/profile',
        data: false,
        options: false
      },
      {
        id: 3,
        label: 'Заявки',
        icon: 'real_estate_agent',
        url: '/user/application/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `Заявка ${props.application.application_number}`,
        icon: 'article',
        url: `/user/application/view?id=${props.application.id}`,
        data: false,
        options: false
      },
    ])
    
    return { breadcrumbs, createDate, updateDate }
  },
})
</script>
