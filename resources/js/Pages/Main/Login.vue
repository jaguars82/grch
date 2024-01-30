<template>
  <q-layout view="hHh Lpr lFf">
    <q-page-container class="bg-grey-2">
      <q-page padding class="row items-center justify-center">
        <div class="row full-width">
          <div class="col-md-8 offset-md-2 col-xs-12 q-pl-md q-pr-md q-pt-sm">
            <q-card flat class="bg-white text-black">
              <div class="row">
                <div class="col-12 col-sm-6">
                  <q-img
                    class="fit"
                    placeholder-src="/img/login-img.svg"
                    src="/img/login-img.svg"
                    spinner-color="white"
                  ></q-img>
                </div>

                <div class="col-12 col-sm-6">
                  <div class="q-pa-md">
                    <div
                      class="text-h6 q-pb-md text-blue-8 text-center text-bold text-uppercase"
                    >
                      Авторизация
                    </div>
                    <q-form
                      @submit="onSubmit"
                      @reset="onReset"
                      class="q-gutter-md"
                    >
                      <q-input
                        outlined
                        v-model="email"
                        label="Электронная почта"
                        lazy-rules
                        :rules="[
                          (val) =>
                            (val && val.length > 0) || 'Пожалуйста, введите email',
                        ]"
                      />

                      <template v-if="loginway === 'pass'">
                        <q-input
                          outlined
                          :type="showPass ? 'text' : 'password'"
                          v-model="password"
                          label="Пароль"
                          lazy-rules
                          :rules="[
                            (val) =>
                              (val !== null && val !== '') ||
                              'Пожалуйста, введите пароль',
                          ]"
                        >
                          <template v-slot:append>
                            <q-icon
                              class="cursor-pointer"
                              :name="showPass ? 'visibility_off' : 'visibility'"
                              @click.stop.prevent="showPass = !showPass"
                            />
                          </template>
                        </q-input>
                      </template>

                      <template v-else-if="loginway === 'otp'">
                        <q-input
                          outlined
                          v-model="otp"
                          label="Код из email"
                          :disable="!codeIsSent"
                          lazy-rules
                          :rules="[
                            (val) =>
                              (val !== null && val !== '') ||
                              'Пожалуйста, введите код',
                          ]"
                        />
                      </template>

                      <div>
                        <q-btn v-if="loginway === 'otp' && !codeIsSent" unelevated label="Выслать код" color="primary" @click="sendCode" />
                        <q-btn v-if="loginway === 'pass' || (loginway === 'otp' && codeIsSent)" unelevated label="Войти" type="submit" color="primary" />
                        <q-btn
                          label="Очистить"
                          type="reset"
                          color="primary"
                          flat
                          class="q-ml-sm"
                        />
                      </div>
                    </q-form>
                  </div>
                </div>
              </div>
            </q-card>
          </div>
          <div class="col-12 q-mt-sm text-center">
            <q-btn size="xs" flat :color="loginway === 'pass' ? 'primary' : 'grey'" @click="loginway = 'pass'" label="Вход по паролю" />
            <q-btn size="xs" flat :color="loginway === 'otp' ? 'primary' : 'grey'" @click="loginway = 'otp'" label="Вход по коду" />
          </div>
        </div>

      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'
//import FlashMessage from '@/Components/Elements/FlashMessage.vue'

export default {
  /*components: {
    FlashMessage
  },*/
  props: {
    model: Object,
    defaultLogin: String,
    error: {
      type: [String, Boolean],
      default: false
    },
  },
  setup (props) {
    const loginway = ref('pass')
    const email = ref(null)
    const password = ref(null)
    const otp = ref('')

    const showPass = ref(false)

    const codeIsSent = ref(false)

    // const flashDialog = ref(false)
    const $q = useQuasar()
    const flashDialogSettings = ref({})

    const sendCode = () => {
      axios.post('/auth/send-code', { email: email.value })
      .then(function (response) {
        if (response.data.error) {
          flashDialogSettings.value.color = 'red'
          switch (response.data.error) {
            case 'bad_email':
              flashDialogSettings.value.messageText = 'Неверный email. Попробуйте ещё раз или обратитесь в службу поддержки.'
              break
            case 'cant_send_code':
              flashDialogSettings.value.messageText = 'Между отправками кода на email должно пройти определеннное время. Попробуйте ещё раз через некоторое время или обратитесь в службу поддержки.'
              break
            default:
              flashDialogSettings.value.messageText = 'Произошла ошибка. Обратитесь в службу поддержки.'
          }
        } else {
          flashDialogSettings.value.color = 'green'
          flashDialogSettings.value.messageText = 'Вам на e-mail отправлено письмо с кодом для входа на сайт'
          codeIsSent.value = true
        }
        // flashDialog.value = true
        $q.notify({
          position: 'top',
          message: flashDialogSettings.value.messageText,
          color: flashDialogSettings.value.color,
          icon: 'info',
          multiLine: false,
        })
      })
      .catch(function (error) {
        // console.log(error)
      })
    }
    const onSubmit = () => {
      Inertia.post('/auth/login', { loginway: loginway.value, email: email.value, password: loginway.value === 'pass' ? password.value : 'not_needed', otp: loginway.value === 'otp' ? otp.value : 'not_needed' })
    }
    const onReset = () => {
      email.value = null
      password.value = null
      otp.value = ''
    }

    const loginError = computed (() => {
      return props.error
    })

    watch (loginError, () => {
      if (loginError !== false) {
        $q.notify({
          position: 'top',
          message: props.error,
          color: 'red',
          icon: 'info',
          multiLine: false,
        })
        codeIsSent.value = false
        otp.value = ''
      }
    })

    return {
      loginway,
      email,
      password,
      otp,
      showPass,
      codeIsSent,
      sendCode,
      onReset,
      onSubmit,
      // flashDialog,
      flashDialogSettings
    }
  }
}
</script>