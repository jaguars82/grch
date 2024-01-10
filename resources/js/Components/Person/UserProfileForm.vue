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
      <div class="col-12 col-sm-6 q-py-xs">
        <q-input outlined v-model="formfields.email" label="Электронная почта" />
      </div>
      <div class="col-12 col-sm-6 q-py-xs">
        <q-input outlined v-model="formfields.phone" label="Телефон" />
      </div>
      <div class="col-12 col-sm-6 q-py-xs">
        <q-input outlined v-model="formfields.telegram_id" label="Telegram ID" />
      </div>
      <div class="col-12 col-sm-6 q-py-xs">
        <q-input outlined v-model="formfields.telegram_chat_id" label="Telegram Chat ID" />
      </div>
      <div class="col-12 q-pr-md q-py-xs">
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
    <div class="row justify-end q-px-md q-pt-md">
      <q-btn type="submit" padding="xs md" unelevated rounded color="primary" icon="done" label="Сохранить" :disable="!canSubmitForm" />
      <q-btn class="q-ml-xs" padding="xs md" unelevated rounded icon="close" label="Отмена" @click="onCancel" />
    </div>
  </q-form>
</template>

<script>
import { ref, computed } from 'vue'
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
    })

    const image = ref('')

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

    return { formfields, image, canSubmitForm, onSubmitForm, onCancel }
  }
}
</script>