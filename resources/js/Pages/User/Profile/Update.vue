<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Редактировать данные профиля">
        <template v-slot:content>
          <div class="q-mt-md"></div>
          <UserProfileForm
            :user="user"
            :actionUrl="`/user/profile/update?id=${user.id}`"
            redirectUrl="/user/profile"
          />
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import UserProfileForm from '@/Components/Person/UserProfileForm.vue'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    UserProfileForm
  },
  props: {
    user: Object,
    messageError: {
      type: [Boolean, String],
      default: false
    },
  },
  setup(props) {
    const { user } = userInfo()

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
        label: 'Редактировать профиль',
        icon: 'manage_accounts',
        url: '/user/profile/update',
        data: false,
        options: false
      },
    ])

    const $q = useQuasar()

    onMounted(() => {
      if (props.messageError) {
        $q.notify({
          position: 'top',
          message: props.messageError,
          color: 'red',
          icon: 'close',
          multiLine: false,
        })
      }
    })

    return { user, breadcrumbs }
  },
})
</script>