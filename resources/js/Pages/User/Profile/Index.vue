<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <q-card class="q-ml-md">
        <q-card-section>
          <p class="text-h3">{{ user.last_name }} {{ user.first_name }} {{ user.middle_name }}</p>
          <p class="text-h5 text-grey-7">{{ user.roleLabel }}</p>
        </q-card-section>
        <q-card-section>
          <p class="text-h6">Контакты</p>
          <div>
            <a v-if="user.phone" :href="`tel:${user.phone}`">
              <div>
              <q-icon class="q-mr-md" name="phone" />
              {{ user.phone }}
              </div>
            </a>              
            <a v-if="user.email" :href="`mailto:${user.email}`">
              <div>
              <q-icon class="q-mr-md" name="email" />
              {{ user.email }}
              </div>
            </a>              
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <inertia-link :href="`/user/profile/update?id=${user.id}`">
          <q-btn padding="xs md" unelevated rounded color="primary" icon="edit" label="Редактировать"></q-btn>
          </inertia-link>
          <q-btn v-if="user.passauth_enabled" class="q-ml-sm" padding="xs md" unelevated rounded color="primary" icon="vpn_key" label="Изменить пароль" @click="openPassDialog"></q-btn>
        </q-card-actions>
      </q-card>

      <q-card v-if="!user.passauth_enabled" class="q-mt-md q-ml-md">
        <q-card-section>
          <p class="text-h3">Активируйте авторизацию по паролю</p>
          <p>Вход в систему по временному коду скоро будет отключен. Пожалуйста, нажмите кнопку "Создать пароль", чтобы активировать возможность авторизации в системе по логину и паролю.</p>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn padding="xs md" unelevated rounded color="primary" icon="vpn_key" label="Создать пароль" @click="openPassDialog"></q-btn>
        </q-card-actions>
      </q-card>

      <q-dialog v-model="createPassDialog" persistent>
        <q-card>
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h4">Задайте пароль для входа в систему</div>
            <q-space />
            <q-btn icon="close" flat round dense v-close-popup />
          </q-card-section>

          <q-card-section>
            <q-input type="password" outlined v-model="formfields.password" label="Введите пароль" />
            <q-input type="password" outlined v-model="formfields.passwordConfirm" label="Подтвердите пароль" />
          </q-card-section>
          <q-card-actions align="right">
            <q-btn padding="xs md" unelevated rounded color="primary" icon="done" label="Сохранить" @click="onSubmitPassword" :disable="!canSubmitPassword"></q-btn>
            <q-btn padding="xs md" unelevated rounded icon="close" label="Отмена" v-close-popup></q-btn>
          </q-card-actions>
        </q-card>
      </q-dialog>

    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import { userInfo } from '@/composables/shared-data'

export default ({
  components: {
    ProfileLayout,
    Breadcrumbs
  },
  props: {
    messageSuccess: {
      type: [Boolean, String],
      default: false
    },
    passSaved: {
      type: Boolean,
      default: false
    }
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
      /*{
        id: 3,
        label: 'Профиль',
        icon: 'account_box',
        url: '/user/profile/index',
        data: false,
        options: false
      },*/
    ])

    const $q = useQuasar()

    onMounted(() => {
      if (props.messageSuccess) {
        Inertia.get('/user/profile', { only: ['user'] })
        $q.notify({
          position: 'top',
          message: props.messageSuccess,
          color: 'green',
          icon: 'done',
          multiLine: false,
        })
      }
    })

    const createPassDialog = ref(false)

    const formfields = ref(
      {
        password: '',
        passwordConfirm: '',
      }
    )

    const canSubmitPassword = computed(() => {
      if (formfields.value.password === '' || formfields.value.passwordConfirm === '') return false
      if (formfields.value.password !== formfields.value.passwordConfirm) return false
      return true
    })

    const openPassDialog = () => {
      formfields.value.password = ''
      formfields.value.passwordConfirm = ''
      createPassDialog.value = true
    }

    const onSubmitPassword = () => {
      createPassDialog.value = false
      Inertia.post(`/user/profile/index`, formfields.value)
      Inertia.on('success', (event) => {
        if (!user.passauth_enabled) {
          Inertia.reload({ only: ['user', 'passSaved'] })
        }
        if (props.passSaved) {
          $q.notify({
            position: 'top',
            message: 'Пароль успешно сохранен',
            color: 'green',
            icon: 'done',
            multiLine: false,
          })
        }
      })
    }

    return { user, breadcrumbs, formfields, openPassDialog, createPassDialog, canSubmitPassword, onSubmitPassword }
  },
})
</script>
