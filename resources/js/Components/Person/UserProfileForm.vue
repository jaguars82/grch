<template>
  <q-form @submit="onSubmitForm">
    <div class="row q-col-gutter-none">
      <div class="col-12 col-md-4 q-py-xs">
        <q-input outlined v-model="formfields.first_name" label="Имя" />
      </div>
      <div class="col-12 col-md-4 q-py-xs">
        <q-input outlined v-model="formfields.middle_name" label="Отчество" />
      </div>
      <div class="col-12 col-md-4 q-py-xs">
        <q-input outlined v-model="formfields.last_name" label="Фамилия" />
      </div>

      <div class="row q-col-gutter-none">

        <div class="col-12 col-sm-7">
          <div class="row q-col-gutter-none">

            <div class="col-12 col-sm-6 q-py-xs" :class="{ 'q-pr-none': $q.screen.xs }">
              <q-input outlined v-model="formfields.email" label="Электронная почта" />
            </div>
            <div class="col-12 col-sm-6 q-py-xs q-pr-none">
              <q-input outlined v-model="formfields.phone" label="Телефон" />
            </div>
            <div class="col-12 col-sm-6 q-py-xs" :class="{ 'q-pr-none': $q.screen.xs }">
              <q-input outlined v-model="formfields.telegram_id" label="Telegram ID" />
            </div>
            <div class="col-12 col-sm-6 q-py-xs q-pr-none">
              <q-input outlined v-model="formfields.telegram_chat_id" label="Telegram Chat ID" />
            </div>
            <div class="col-12 q-py-xs">
              <q-file
                outlined
                v-model="image"
                label="Перетащите или загрузите фото"
                use-chips
                accept=".jpg, image/*"
              >
                <template v-slot:prepend>
                  <q-icon name="attach_file" />
                </template>
                <template v-slot:file="{ index, file }">
                  <q-chip
                    :removable="true"
                    @remove="image = ''"
                  >
                    <q-avatar>
                      <q-icon name="photo" />
                    </q-avatar>
                    <div class="ellipsis relative-position">
                      {{ file.name }}
                    </div>
                  </q-chip>
                </template>
              </q-file>
            </div>

          </div>
        </div>

        <div class="col-12 q-py-xs col-sm-5">
          <q-img v-if="photoPreview" class="rounded-borders photo" :src="photoPreview" />
          <q-img v-else :fit="formfields.is_photo_reset ? 'scale-down' : ''" class="rounded-borders photo" :src="user.photo && !formfields.is_photo_reset ? `/uploads/${user.photo}` : '/img/blank-person.svg'">
            <q-icon
              v-if="!formfields.is_photo_reset"
              class="absolute cursor-pointer all-pointer-events"
              size="28px"
              name="close"
              color="white"
              style="top: 4px; right: 4px"
              @click="onResetPhoto"
            >
              <q-tooltip>
                Удалить текущую фотографию
              </q-tooltip>
            </q-icon>
            <q-icon
              v-if="formfields.is_photo_reset"
              class="absolute cursor-pointer all-pointer-events"
              size="28px"
              name="refresh"
              color="grey-7"
              style="top: 4px; right: 4px"
              @click="onRestorePhoto"
            >
              <q-tooltip>
                Восстановить фото
              </q-tooltip>
            </q-icon>
          </q-img>
        </div>

      </div>
    </div>

    <div class="row justify-end q-px-md q-pt-lg">
      <q-btn type="submit" padding="xs md" unelevated rounded color="primary" icon="done" label="Сохранить" :disable="!canSubmitForm" />
      <q-btn class="q-ml-xs" padding="xs md" unelevated rounded icon="close" label="Отмена" @click="onCancel" />
    </div>
  </q-form>
</template>

<script>
import { ref, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    user: Object,
    actionUrl: String,
    redirectUrl: String
  },
  setup (props) {
    const formfields = ref({
      agency_id: props.user.agency_id ? props.user.agency_id : '',
      developer_id: props.user.developer_id ? props.user.developer_id : '',
      first_name: props.user.first_name ? props.user.first_name : '',
      middle_name: props.user.middle_name ? props.user.middle_name : '',
      last_name: props.user.last_name ? props.user.last_name : '',
      email: props.user.email ? props.user.email : '',
      phone: props.user.phone ? props.user.phone : '',
      telegram_id: props.user.telegram_id ? props.user.telegram_id : '',
      telegram_chat_id: props.user.telegram_chat_id ? props.user.telegram_chat_id : '',
      photo: '',
      is_photo_reset: false
    })

    const image = ref('')

    const photoPreview = ref('')

    watch(image, () => {
      if (image.value) {
        let reader = new FileReader()
        reader.readAsDataURL(image.value)
        reader.onload = function(e) {
          let data = reader.result
          photoPreview.value = data
        }
      } else {
        photoPreview.value = ''
      }
    })

    const onResetPhoto = () => {
      formfields.value.is_photo_reset = true
    }

    const onRestorePhoto = () => {
      formfields.value.is_photo_reset = false
    }

    const canSubmitForm = computed(() => {
      if (formfields.value.first_name === '') return false
      if (formfields.value.last_name === '') return false
      if (formfields.value.email === '') return false
      return true
    })

    const onSubmitForm = () => {
      formfields.value.photo = image.value
      Inertia.post(props.actionUrl, formfields.value, { forceFormData: true })
    }

    const onCancel = () => {
      Inertia.get(props.redirectUrl)
    }

    return { formfields, image, photoPreview, onResetPhoto, onRestorePhoto, canSubmitForm, onSubmitForm, onCancel }
  }
}
</script>

<style scoped>
.photo {
  max-height: 200px;
}
</style>